# Upgrading consumers — connection pooling and telemetry (Phase 0)

Applies to `spatial/doctrine` and `spatial/core` after the Phase 0 pooling and
telemetry fixes. Consuming services: `nx_api`, `nx_suite_api`, `nx_notify`,
`nx_identity_api`, `nx_intelligence_api`.

Two problems motivated these changes:

1. A closed `EntityManager` was dropped instead of being returned to the pool,
   so each occurrence permanently cost one slot. `ClientPool::get()` was called
   with no timeout, so once all slots were gone the worker blocked forever on
   every database request. Doctrine closes an `EntityManager` on any failed
   `flush()`, which is routine.
2. Pool size was hardcoded to `10`, ignoring `poolSize` in `doctrine.yaml`, and
   no service set `worker_num`. OpenSwoole therefore ran one worker per CPU
   core, and total connections scaled with host size:
   `worker_num x pools x poolSize`.

## What changed in the framework

`Spatial\Entity\DbConnection`

- `poolSize` and `connectionDelay` are now read from the connection block.
  `connectionDelay` becomes the checkout timeout, in seconds.
- Checkout validates `isOpen()` and swaps a dead `EntityManager` for a fresh
  one instead of handing it out or dropping it.
- Exhaustion throws `Spatial\Entity\Exception\PoolExhaustedException`, which
  `ErrorHandlingMiddleware` renders as `503` with `Retry-After`.
- Release rolls back an abandoned transaction and calls `clear()`, so a pooled
  `EntityManager` no longer carries its identity map — or another request's
  entities — into the next request.
- New `withEntityManager(callable)` pairs checkout and release for you.
- New `warmup()` and `stats()`; `closeAllConnection()` no longer relies on
  `ClientPool::close()`, whose drain loop has no upper bound.
- **Pools are created empty.** `App::boot()` runs in the Swoole master before
  workers fork, so filling there built `EntityManager`s pre-fork and handed
  every worker a copy. Slots are now created on demand inside the worker.

`Spatial\Telemetry\*`

- `OtelProviderFactory::create()` is idempotent.
- New `registerFlushTimer()`, `forceFlush()` and `shutdown()`. `ExportingReader`
  has no internal schedule, so without the timer metrics only left the process
  when it exited — which for a Swoole worker is approximately never.
- `http.route` is now a route template, not the raw path. Every distinct URL
  used to become its own metric series.
- Stable HTTP semantic conventions (`http.request.method`,
  `http.response.status_code`, `server.address`, `client.address`,
  `user_agent.original`). Set `OTEL_SEMCONV_STABILITY_OPT_IN=http/dup` to emit
  both old and new names while dashboards are migrated, or `http` for old only.

## Required changes per service

### 1. Set `worker_num` explicitly — do this first

This is the change that caps the connection count. In `public/index.php`, after
`$http = new Server(...)`:

```php
$http->set([
    'worker_num' => (int)(getenv('SWOOLE_WORKER_NUM') ?: 4),
    'max_request' => 0,
    'enable_coroutine' => true,
]);
```

Budget check before choosing a value:

```
connections per service = worker_num x (pools in that service) x poolSize
```

`nx_api` has two pools (`social_db_pool`, `rent_db_pool`); the others have one.
Sum across every service sharing the database and compare against
`max_connections`, leaving headroom for migrations, admin sessions and backups.

### 2. Add the worker lifecycle hooks

```php
use Spatial\Entity\DbConnection;
use Spatial\Telemetry\OtelProviderFactory;

$http->on("workerStart", function (Server $server, int $workerId) {
    try {
        DbConnection::warmup();
    } catch (Throwable $e) {
        error_log("Worker {$workerId} pool warmup skipped: " . $e->getMessage());
    }
    OtelProviderFactory::registerFlushTimer();
});

$http->on("workerStop", function (Server $server, int $workerId) {
    DbConnection::closeAllConnection();
    OtelProviderFactory::shutdown();
});
```

Creating an `EntityManager` does not open a socket — DBAL connects lazily on
first query — so `warmup()` is cheap.

### 3. Delete the SIGTERM handler

`pcntl_signal` never fires here: it needs a `pcntl_signal_dispatch()` pump that
an event loop does not run. Remove it and add nothing in its place — the
OpenSwoole master installs its own SIGTERM handler, which shuts the server down
gracefully and so fires `workerStop`, draining pools and flushing telemetry.

Do **not** reach for `Process::signal()` as the replacement:

```php
// Wrong. Called before start(), this creates the event loop, and
// Server::start() then refuses with "eventLoop has already been created".
// Supervisor restart-loops the worker until it gives up in a FATAL state.
Process::signal(SIGTERM, fn() => $http->shutdown());

$http->start();
```

### 4. Simplify the DB traits

The recursive retry is now dead code — `getConnection()` never returns a closed
`EntityManager`. Remove it so the old shape cannot be copied into new services:

```php
// Before — dropped the closed EM, permanently costing a pool slot
private function getConnection(): EntityManagerInterface
{
    $em = $this->socialDb->getConnection();
    if (!$em->isOpen()) {
        return $this->getConnection();
    }
    return $em;
}

// After
private function getConnection(): EntityManagerInterface
{
    return $this->socialDb->getConnection();
}
```

Applies to `SocialTrait`, `SuiteTrait`, `IdentityTrait` and `NotifyTrait`.

### 5. Release is now automatic — nothing to do

This step used to say "every `getEntityManager()` must release in a `finally`".
That advice was correct and unfollowable: across the five services 652 handlers
take an `EntityManager` and exactly one wraps it in `try/finally`, so any early
return or exception stranded a pool slot for the life of the worker, and eight
of them starved it.

Checkouts are now scoped to the coroutine instead. The first `getConnection()`
in a request leases an `EntityManager` and registers a `defer` hook; later calls
in the same request get the same instance back, and it returns to the pool when
the request ends — early return, exception, or normal completion alike.

Practically:

- **Existing `releaseConnection()` calls are fine.** They still reset the
  `EntityManager`, rolling back an abandoned transaction and clearing the
  identity map. They no longer return the slot, so calling one is no longer the
  difference between a healthy worker and a starved one.
- **Missing `releaseConnection()` calls are now harmless.** No migration needed.
- **`withEntityManager()` still works** and is still the clearest way to express
  a scoped unit of work.
- **One exception: outside a coroutine** — console commands, queue consumers,
  migrations — there is no `defer` to hang the release on, so the old contract
  stands. Pair `getConnection()` with `releaseConnection()` in `bin/` scripts, or
  use `withEntityManager()`.

### 6a. Done in `nx_api`: the Rent module was removed

`RentDB` read `connections['rent']`, which no `doctrine.yaml` defined, so
constructing it threw a `TypeError`. `RentApiModule` was never listed in
`AppModule::imports`, so the whole path was unreachable. 38 files were removed:
`src/presentation/RentApi/`, `src/core/Application/Logics/Rent/`,
`src/core/Domain/Rent/` and `src/infrastructure/Resource/RentDB.php`. The
similarly named `Domain/Ori/Rent`, `Domain/Suite/Rent` and
`Logics/Control/Rental` are unrelated and were left in place.

### 6b. Done in `nx_api`: the `Common\Exceptions` hierarchy now works

All eight classes lived in `src/common/Exceptions/ApiExceptions.php`, violating
the `Common\ => src/common/` PSR-4 rule, so Composer skipped every one of them
and `class_exists()` returned false for all. They are now one class per file.

Splitting alone was not enough. `ExceptionHandlerMiddleware::categorizeException()`
never checked `ApiException`, so a `ValidationException` fell through to the
default branch and returned **500 instead of 400**; only `Unauthorized` and
`Forbidden` worked, and then only by accident, via `str_contains()` on the class
name. The middleware now checks two things before anything else:

- `ApiException` — uses the exception's own `getHttpStatus()` and
  `getErrorCode()`, which covers the whole hierarchy in one branch.
- `HttpAwareExceptionInterface` — covers framework exceptions, notably
  `PoolExhaustedException`. **Without this, Phase 0's pool-exhaustion path
  returned 500 in `nx_api`**, because this service uses its own middleware
  rather than the framework's `ErrorHandlingMiddleware`.

Referencing the framework interface is safe before `spatial/core` is updated:
`instanceof` against an undefined interface returns false without invoking the
autoloader, so the branch is simply inert until the dependency bump. Both states
are verified.

`ApiException` gained an optional `$retryAfter`, emitted as a `Retry-After`
header (429 and 503 set it). `jsonResponse()` now constructs the response with
its headers and body in one call rather than writing into `getBody()`, which
left the stream pointer at EOF.

Verified end to end: 400/401/403/404/409/429/500/503 all map correctly, bodies
are non-empty, 4xx logs at warning and 5xx at error.

### 6. Fix `NotifyDB` and the other eager constructors

`nx_notify`'s `NotifyDB::__construct()` calls `$this->getConnection()`. During
`App::boot()` that leases a slot in the master process which is never returned,
so every forked worker starts one slot down. Drop the constructor checkout and
lease per request instead. Same pattern in `spatial`'s own `AppDB` and
`IdentityDB` templates.

### 7. Reconcile the OTel environment variables

`.env.example` currently sets both the extension's autoload SDK and the manual
OTLP SDK:

```
OTEL_PHP_AUTOLOAD_ENABLED=true
OTEL_TRACES_EXPORTER=console
OTEL_METRICS_EXPORTER=console
OTEL_LOGS_EXPORTER=console
```

No `opentelemetry-auto-*` package is installed, so the autoload path
instruments nothing while the console exporter settings conflict with the OTLP
path that actually runs. Remove the four lines and keep
`OTEL_EXPORTER_OTLP_ENDPOINT`. Optionally add:

```
OTEL_METRIC_EXPORT_INTERVAL=60000
OTEL_SEMCONV_STABILITY_OPT_IN=http/dup
```

`spatial/README.md` documents `OTEL_ENABLED` and `OTEL_ENDPOINT`, which nothing
reads — the endpoint variable is `OTEL_EXPORTER_OTLP_ENDPOINT`.

## 8. Optional: the coroutine PostgreSQL driver

`spatial/doctrine` now ships its own coroutine driver at
`\Spatial\Entity\Driver\PgSQL\Driver`. It is opt-in, and services are unaffected
until they set `driverClass`.

### Why it matters

OpenSwoole publishes no PDO PostgreSQL hook — there is no `HOOK_PDO_PGSQL`, and
`HOOK_ALL` does not cover it. Measured in a running container, two concurrent
one-second queries on `pdo_pgsql` take two seconds, not one: a query blocks the
whole worker. So today, request concurrency equals `worker_num`, coroutines buy
no database parallelism, and `poolSize` above 1 is decorative.

With this driver the same test finishes in about one second, and eight
concurrent queries take about the time of one. Concurrency becomes
`worker_num x poolSize`, which is why `poolSize` and the connection budget only
start meaning what they say once it is enabled.

### What it is

A fork of [opsway/doctrine-dbal-swoole-pgsql-driver][opsway] 4.0.0 (MIT).
Upstream's design is preserved — the pooled connection binds to the coroutine
context and returns via `defer` — but it was forked rather than depended upon
because it has had no commit since June 2024, ships no tests, is pinned to
`doctrine/dbal ^3.2`, and targets an OpenSwoole PostgreSQL API that no longer
exists.

That last point matters if you were considering enabling the upstream package:
on OpenSwoole 26 it does not work at all. `prepare($name, $sql)` raises
`ArgumentCountError`, `$connection->execute()` and the connection-level fetch
methods were removed, and `query()` returns an object where the code tests for a
resource — so every successful query is reported as a connection failure. Every
parameterised query would fail. `Statement`, `Result` and the query paths in
`Connection` were rewritten against the current API, and four genuine bugs were
fixed, including a pool race that only appears under contention. The details are
in `src/Driver/PgSQL/README.md`.

### Enabling it

In the relevant `doctrine.yaml` connection block:

```yaml
driverClass: \Spatial\Entity\Driver\PgSQL\Driver
```

Then re-check the connection budget: each worker can now hold `poolSize`
concurrent connections rather than roughly one, so the ceiling per service
becomes `worker_num x pools x poolSize`. Lower `worker_num` as you raise
effective concurrency, and confirm the total against the server's
`max_connections`.

Before enabling it in a service, run the conformance test in that container —
it exercises parameterised queries, transactions, SQLSTATE mapping, concurrency
and the exhaustion path:

```bash
php vendor/spatial/doctrine/tests/driver-conformance.php
```

Two limitations to know: only positional `?` placeholders are rewritten to
`$1, $2`, so named parameters in raw DBAL SQL are unsupported (no service uses
them today), and binary or large-object parameters are rejected rather than
silently corrupted.

[opsway]: https://github.com/opsway/doctrine-dbal-swoole-pgsql-driver

## Still outstanding

Not addressed here, tracked for Phase 1 and 2:

- The coroutine driver is written and tested but not enabled in any service.
  Turning it on is a per-service decision that comes with a `worker_num`
  re-budget.
- Prepared statements are not cached across queries, so each parameterised query
  costs an extra `PREPARE` round trip. Correct, but leaves throughput on the
  table.

- **12 more PSR-4 violations remain in `nx_api`**, and two of them are live
  production bugs rather than latent ones:
  - `src/core/Application/Logics/Control/interverse/` is lowercase while the
    namespace is `Control\Interverse`. macOS is case-insensitive so it resolves
    locally, but the Linux containers are not — those four classes do not
    autoload in production, and `ControlApi/Controllers/InterverseController`
    references them. Fix with a two-step `git mv` to `Interverse/`.
  - `BasicAuthMiddleware .php` and `RateLimitMiddleware .php` have a space
    before the extension, so neither autoloads anywhere. `RateLimitMiddleware`
    has a referencing file.
  - `Infrastructure\Cache\RedisCacheService` declares itself as
    `NxApi\Infrastructure\Cache\...`, a prefix absent from the autoload map.
  - `src/common/Compat/phpstan-stubs.php` declares five vendor classes
    (`OpenSwoole\Coroutine\Channel`, three `Spatial\...` interfaces). Being
    skipped is what keeps it from colliding with the real ones, so add it to
    `exclude-from-classmap` rather than "fixing" it.
- No coroutine-safe OTel `ContextStorage`, so concurrent requests in one worker
  can parent spans onto each other under `HOOK_ALL`.
- No W3C trace context propagation in either direction.
- No Doctrine/DBAL instrumentation.
- Metadata, query and result caches are declared in `doctrine.yaml` but never
  wired into `Configuration`.
- `src/DoctrineConfig.php` is dead and would fatal if constructed. The four
  coroutine stubs that shared this problem — `CoroutineConnection`,
  `CoroutineDriverMiddleware`, `CoroutineEntityManager` and
  `ReopeningEntityManager` — have been removed.

## Verifying

Offline, no service required:

```bash
php spatial-doctrine/tests/pool-lease-test.php      # lease accounting, 28 checks
php spatial-core/test/route-normalizer-test.php     # metric cardinality
```

Inside a service container, which has the openswoole extension:

```bash
php vendor/spatial/doctrine/tests/coroutine-scope-test.php   # scoped checkouts, 15 checks
php vendor/spatial/doctrine/tests/driver-conformance.php     # coroutine driver, 21 checks
```

The driver test needs a reachable database and creates and drops one table,
`spatial_driver_probe`.

Against a running service, `tools/verify-phase0.php` drives load and watches
what PostgreSQL actually does. It checks that connections plateau at the
expected ceiling, that no request hangs (the old failure mode was an untimed
`ClientPool::get()` blocking forever), that saturation sheds as `503` with
`Retry-After` rather than `500`, and that the high-water mark does not grow
between identical load cycles.

```bash
php tools/verify-phase0.php \
  --url=http://localhost:8080/<a route that touches the database> \
  --datname=oriDB \
  --db-host=127.0.0.1 --db-user=postgres --db-pass="$DB_PASSWORD" \
  --expect-ceiling=16 \
  --concurrency=64 --duration=15 --cycles=2
```

`--expect-ceiling` is the total for that **database**, across every process that
pools against it. At `worker_num=2`:

| Database         | Service                                        | Ceiling |
| ---------------- | ---------------------------------------------- | ------- |
| `oriDB`          | nx_api                                         | 16      |
| `suiteDB`        | nx_suite_api + publish-outbox + lead-notifier  | 32      |
| `notify_test`    | nx_notify                                      | 16      |
| `identityDB`     | nx_identity_api                                | 16      |
| `intelligenceDB` | nx_intelligence_api + airleads-sourcing        | 6       |

Add `--dry-run` to validate arguments and database access without generating
load. Use a route that actually leases an EntityManager — a static health
endpoint exercises none of this.

Two things the harness deliberately does not assert:

- **Connections do not return to baseline after load.** A released
  EntityManager keeps its DBAL connection open for reuse, so settling above
  baseline is correct. A leak shows up as the peak growing between cycles.
- **Load shedding may not trigger** if the pool absorbs the offered load. That
  is a pass, not a gap; raise `--concurrency` to exercise the 503 path.

After deploying, watch:

- `SELECT count(*) FROM pg_stat_activity` against your budget.
- `DbConnection::stats()` — `inUse` that only ever climbs means a lease leak;
  `timeouts` above zero means the pool is too small or requests hold leases too
  long.
- `http.server.duration` actually arriving in Prometheus, with a bounded number
  of `http.route` values.
