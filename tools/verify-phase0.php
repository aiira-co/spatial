<?php

declare(strict_types=1);

/**
 * Phase 0 verification harness — connection pooling under load.
 *
 * Everything in Phase 0 was verified statically (php -l) and with stubbed unit
 * tests. This script closes the gap by driving a running service and watching
 * what PostgreSQL actually does, checking the four behaviours that changed:
 *
 *   ceiling   Connections plateau at worker_num x poolSize instead of growing
 *             with load. Catches worker_num or poolSize not taking effect.
 *   no-hang   Every request returns. Before Phase 0, ClientPool::get() had no
 *             timeout, so a request that found the pool empty blocked forever.
 *   shed      Saturation returns 503 with Retry-After, not 500 and not a
 *             timeout. Confirms PoolExhaustedException reaches the middleware.
 *   no-leak   The high-water mark does not grow between identical load cycles.
 *             This is the regression test for the dropped-EntityManager leak.
 *             Note it is deliberately NOT "connections return to baseline":
 *             a pooled EntityManager keeps its DBAL connection open after
 *             release, so the count settling above baseline is correct
 *             behaviour. A leak shows up as cycle 2 peaking higher than
 *             cycle 1, never as a failure to return to zero.
 *
 * Requires only ext-curl and ext-pdo_pgsql. Read-only against the database.
 *
 * Example:
 *   php tools/verify-phase0.php \
 *     --url=http://localhost:8080/api/v1/health \
 *     --datname=oriDB \
 *     --db-host=127.0.0.1 --db-user=postgres --db-pass="$DB_PASSWORD" \
 *     --expect-ceiling=16
 *
 * Exits 0 when every check passes, 1 otherwise.
 */

/* --------------------------------------------------------------- options */

$defaults = [
    'url' => null,
    'datname' => null,
    'db-host' => '127.0.0.1',
    'db-port' => '5432',
    'db-user' => 'postgres',
    'db-pass' => '',
    // worker_num x poolSize for the service under test.
    'expect-ceiling' => null,
    // Slack for a psql session or a migration running on the same database.
    'tolerance' => '3',
    'concurrency' => '64',
    'duration' => '15',
    // A request taking longer than this counts as a hang, not slowness.
    'hang-threshold' => '20',
    'cycles' => '2',
    'settle' => '5',
    'method' => 'GET',
    'header' => [],
    'dry-run' => false,
];

$opts = $defaults;
foreach (array_slice($argv, 1) as $arg) {
    if (!preg_match('/^--([a-z-]+)(?:=(.*))?$/', $arg, $m)) {
        fwrite(STDERR, "Unrecognised argument: {$arg}\n");
        exit(2);
    }

    [$_, $key] = $m;
    $value = $m[2] ?? true;

    if (!array_key_exists($key, $opts)) {
        fwrite(STDERR, "Unknown option: --{$key}\n");
        exit(2);
    }

    if ($key === 'header') {
        $opts['header'][] = $value;
        continue;
    }

    $opts[$key] = $value;
}

foreach (['url', 'datname', 'expect-ceiling'] as $required) {
    if ($opts[$required] === null) {
        fwrite(STDERR, "Missing required option: --{$required}\n\nSee the header of this file for usage.\n");
        exit(2);
    }
}

$expectCeiling = (int)$opts['expect-ceiling'];
$tolerance = (int)$opts['tolerance'];
$concurrency = (int)$opts['concurrency'];
$duration = (float)$opts['duration'];
$hangThreshold = (float)$opts['hang-threshold'];
$cycles = (int)$opts['cycles'];
$settle = (int)$opts['settle'];

/* ------------------------------------------------------------- reporting */

$results = [];

function record(string $name, bool $ok, string $detail): void
{
    global $results;
    $results[] = ['name' => $name, 'ok' => $ok, 'detail' => $detail];
    printf("  %s  %-34s %s\n", $ok ? 'PASS' : 'FAIL', $name, $detail);
}

function section(string $title): void
{
    echo "\n" . $title . "\n" . str_repeat('-', max(40, strlen($title))) . "\n";
}

/* -------------------------------------------------------------- database */

/**
 * Connects to the `postgres` maintenance database, not the target, so this
 * sampler's own backend is never counted in the numbers it reports.
 */
function connectSampler(array $opts): PDO
{
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=postgres;connect_timeout=5',
        $opts['db-host'],
        $opts['db-port'],
    );

    return new PDO($dsn, $opts['db-user'], $opts['db-pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

function backendCount(PDO $pdo, string $datname): int
{
    static $stmt = null;
    $stmt ??= $pdo->prepare(
        'SELECT count(*) FROM pg_stat_activity WHERE datname = :d AND pid <> pg_backend_pid()'
    );
    $stmt->execute(['d' => $datname]);

    return (int)$stmt->fetchColumn();
}

/* ------------------------------------------------------------------ load */

/**
 * Drive $concurrency requests in flight for $duration seconds, sampling the
 * backend count as it goes.
 *
 * @return array{statuses: array<int,int>, latencies: float[], samples: int[], errors: string[]}
 */
function drive(
    string $url,
    string $method,
    array $headers,
    int $concurrency,
    float $duration,
    float $hangThreshold,
    ?PDO $pdo,
    ?string $datname,
): array {
    $multi = curl_multi_init();
    $started = [];
    $statuses = [];
    $latencies = [];
    $errors = [];
    $samples = [];

    $makeHandle = static function () use ($url, $method, $headers, $hangThreshold) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            // Deliberately above hang-threshold so a hang is observed as a slow
            // response rather than masked by curl aborting first.
            CURLOPT_TIMEOUT => (int)ceil($hangThreshold) + 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_NOSIGNAL => true,
        ]);

        return $ch;
    };

    for ($i = 0; $i < $concurrency; $i++) {
        $ch = $makeHandle();
        curl_multi_add_handle($multi, $ch);
        $started[(int)$ch] = microtime(true);
    }

    $deadline = microtime(true) + $duration;
    $lastSample = 0.0;
    $accepting = true;

    do {
        curl_multi_exec($multi, $running);
        curl_multi_select($multi, 0.05);

        $now = microtime(true);

        if ($pdo !== null && $datname !== null && $now - $lastSample >= 0.25) {
            $lastSample = $now;
            $samples[] = backendCount($pdo, $datname);
        }

        if ($now >= $deadline) {
            $accepting = false;
        }

        while ($info = curl_multi_info_read($multi)) {
            $ch = $info['handle'];
            $key = (int)$ch;
            $elapsed = microtime(true) - ($started[$key] ?? $now);

            if ($info['result'] !== CURLE_OK) {
                $errors[] = curl_error($ch) ?: ('curl code ' . $info['result']);
            } else {
                $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
                $statuses[$status] = ($statuses[$status] ?? 0) + 1;
                $latencies[] = $elapsed;

                // Retry-After is what makes a 503 a load-shed rather than a fault.
                if ($status === 503) {
                    $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                    if ($headerSize === 0) {
                        // Headers were not captured; recorded separately below.
                    }
                }
            }

            curl_multi_remove_handle($multi, $ch);
            curl_close($ch);
            unset($started[$key]);

            if ($accepting) {
                $next = $makeHandle();
                curl_multi_add_handle($multi, $next);
                $started[(int)$next] = microtime(true);
            }
        }
    } while ($running > 0 || $started !== []);

    curl_multi_close($multi);

    return [
        'statuses' => $statuses,
        'latencies' => $latencies,
        'samples' => $samples,
        'errors' => $errors,
    ];
}

/** Single request that also returns response headers, for Retry-After checks. */
function probe(string $url, string $method, array $headers, float $timeout = 30.0): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => (int)$timeout,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_NOSIGNAL => true,
    ]);

    $start = microtime(true);
    $body = curl_exec($ch);
    $elapsed = microtime(true) - $start;
    $error = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $headerSize = (int)curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    return [
        'status' => $status,
        'error' => $error,
        'elapsed' => $elapsed,
        'headers' => $body === false ? '' : substr((string)$body, 0, $headerSize),
    ];
}

/* ------------------------------------------------------------------- run */

echo "Spatial Phase 0 verification\n";
printf("  target      %s %s\n", $opts['method'], $opts['url']);
printf("  database    %s on %s:%s\n", $opts['datname'], $opts['db-host'], $opts['db-port']);
printf("  ceiling     %d (+%d tolerance)\n", $expectCeiling, $tolerance);
printf("  load        %d concurrent for %.0fs x %d cycle(s)\n", $concurrency, $duration, $cycles);

try {
    $pdo = connectSampler($opts);
} catch (Throwable $e) {
    fwrite(STDERR, "\nCannot reach PostgreSQL: " . $e->getMessage() . "\n");
    exit(2);
}

$serverMax = (int)$pdo->query('SHOW max_connections')->fetchColumn();
printf("  server      max_connections = %d\n", $serverMax);

if ($opts['dry-run'] !== false) {
    echo "\nDry run: configuration and database access are valid. Exiting before load.\n";
    exit(0);
}

section('1. Service reachable');
$first = probe($opts['url'], $opts['method'], $opts['header']);
if ($first['error'] !== '') {
    record('reachable', false, $first['error']);
    echo "\nCannot continue without a reachable service.\n";
    exit(1);
}
record(
    'reachable',
    $first['status'] < 500,
    sprintf('HTTP %d in %.0f ms', $first['status'], $first['elapsed'] * 1000),
);

$baseline = backendCount($pdo, (string)$opts['datname']);
record('baseline measured', true, sprintf('%d backend(s) on %s while idle', $baseline, $opts['datname']));

$peakOverall = $baseline;
$cyclePeaks = [];
$cycleSettled = [];

for ($cycle = 1; $cycle <= $cycles; $cycle++) {
    section(sprintf('%d. Load cycle %d of %d', $cycle + 1, $cycle, $cycles));

    $run = drive(
        (string)$opts['url'],
        (string)$opts['method'],
        $opts['header'],
        $concurrency,
        $duration,
        $hangThreshold,
        $pdo,
        (string)$opts['datname'],
    );

    $total = array_sum($run['statuses']);
    $peak = $run['samples'] === [] ? $baseline : max($run['samples']);
    $peakOverall = max($peakOverall, $peak);

    ksort($run['statuses']);
    $statusLine = [];
    foreach ($run['statuses'] as $code => $n) {
        $statusLine[] = "{$code}x{$n}";
    }

    printf(
        "  %d requests, statuses: %s%s\n",
        $total,
        implode(' ', $statusLine) ?: 'none',
        $run['errors'] === [] ? '' : sprintf(', %d transport error(s)', count($run['errors'])),
    );

    // -- ceiling. expect-ceiling is the total for this database across every
    // process that pools against it, so baseline is already inside it.
    $cyclePeaks[] = $peak;
    record(
        'connections stay under ceiling',
        $peak <= $expectCeiling + $tolerance,
        sprintf('peak %d, expected <= %d', $peak, $expectCeiling + $tolerance),
    );

    // -- no-hang
    $hung = array_filter($run['latencies'], static fn(float $l): bool => $l >= $hangThreshold);
    record(
        'no request hung',
        count($hung) === 0 && $run['errors'] === [],
        count($hung) === 0
            ? sprintf('slowest %.2fs', $run['latencies'] === [] ? 0 : max($run['latencies']))
            : sprintf('%d request(s) exceeded %.0fs', count($hung), $hangThreshold),
    );

    // -- shed, not fail
    $fiveHundreds = $run['statuses'][500] ?? 0;
    record(
        'no 500s under saturation',
        $fiveHundreds === 0,
        $fiveHundreds === 0 ? 'none' : sprintf('%d request(s) returned 500', $fiveHundreds),
    );

    $shed = $run['statuses'][503] ?? 0;
    if ($shed > 0) {
        $sample = probe($opts['url'], $opts['method'], $opts['header']);
        $hasRetryAfter = stripos($sample['headers'], 'retry-after:') !== false;
        record(
            '503s carry Retry-After',
            $sample['status'] !== 503 || $hasRetryAfter,
            $sample['status'] === 503
                ? ($hasRetryAfter ? 'present' : 'missing')
                : sprintf('%d shed during load; pool recovered before re-probe', $shed),
        );
    } else {
        printf("  ....  %-34s %s\n", 'load shedding not exercised', 'pool absorbed the load; raise --concurrency to test 503s');
    }

    // Settle so idle pooled connections are distinguishable from in-flight
    // work. Staying above baseline here is expected: released EntityManagers
    // keep their connection open for reuse.
    sleep($settle);
    $after = backendCount($pdo, (string)$opts['datname']);
    $cycleSettled[] = $after;
    printf(
        "  ....  %-34s %s\n",
        'settled',
        sprintf('%d backend(s) after %ds idle (baseline %d, peak %d)', $after, $settle, $baseline, $peak),
    );
}

if ($cycles > 1) {
    section('Leak detection across cycles');

    $peakGrowth = end($cyclePeaks) - $cyclePeaks[0];
    record(
        'high-water mark does not grow',
        $peakGrowth <= $tolerance,
        sprintf('peaks %s (growth %+d)', implode(' -> ', array_map('strval', $cyclePeaks)), $peakGrowth),
    );

    $idleGrowth = end($cycleSettled) - $cycleSettled[0];
    record(
        'idle count does not grow',
        $idleGrowth <= $tolerance,
        sprintf('idle %s (growth %+d)', implode(' -> ', array_map('strval', $cycleSettled)), $idleGrowth),
    );
} else {
    section('Leak detection across cycles');
    printf("  ....  %-34s %s\n", 'skipped', 'leak detection needs --cycles=2 or more');
}

section('Summary');
$failed = array_filter($results, static fn(array $r): bool => !$r['ok']);
printf("  peak backends observed: %d (ceiling %d, server max %d)\n", $peakOverall, $expectCeiling, $serverMax);
printf("  %d check(s) run, %d failed\n", count($results), count($failed));

if ($failed !== []) {
    echo "\nFailed checks:\n";
    foreach ($failed as $r) {
        printf("  - %s: %s\n", $r['name'], $r['detail']);
    }
    exit(1);
}

echo "\nAll checks passed.\n";
exit(0);
