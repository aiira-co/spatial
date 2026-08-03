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

### 7. Reconcile the OTel environment variables — done

`.env.example` files no longer enable the extension autoload SDK. Keep the
manual path only:

```
OTEL_EXPORTER_OTLP_ENDPOINT=http://otel-collector:4318
OTEL_METRIC_EXPORT_INTERVAL=60000
OTEL_TRACES_SAMPLER=parentbased_traceidratio
OTEL_TRACES_SAMPLER_ARG=1.0
OTEL_SEMCONV_STABILITY_OPT_IN=http/dup
```

Do not set `OTEL_PHP_AUTOLOAD_ENABLED` — it conflicts with
`OtelProviderFactory`. Sampler env vars are honoured by
`SamplerFactory` inside `OtelProviderFactory::create()`.

## 8. The coroutine PostgreSQL driver — do not enable it yet

`spatial/doctrine` ships a coroutine driver at
`\Spatial\Entity\Driver\PgSQL\Driver`. It is opt-in, no service sets
`driverClass`, and **none should**: OpenSwoole 26.2 cannot pool connections for
it. Read this section before deciding it looks like the answer to a throughput
problem, because it reads that way and it is not.

### The blocker

A coroutine PostgreSQL connection may only be used by the coroutine that used it
first. Give it to a second one — which is what a pool does — and its epoll
registration fails with `ReactorEpoll::add(): failed to add events ... File
exists`, then the worker segfaults. Reproduced with plain
`OpenSwoole\Coroutine\PostgreSQL` and no Doctrine, pool or framework code in the
path, so it is the client:

| Pattern                                  | Result             |
| ---------------------------------------- | ------------------ |
| One connection per coroutine, no handoff | 8242 q/s, 0 errors |
| Connections shared between coroutines    | segfault (139)     |
| `pdo_pgsql`, for reference               | 2400 q/s           |

Enabling it anyway does not fail cleanly. The driver retries a failed checkout,
so requests still return 200 while throughput collapses — a first attempt in
`nx_api` served 600/600 at 124 req/s against 700 req/s on `pdo_pgsql`, which
looks like a slow database rather than a broken driver.

The payoff is genuine, about 3.4x `pdo_pgsql` without blocking the worker, so
revisit this when upstream fixes connection reuse. The only shape that works
today is one connection per coroutine and no pool, which ties the connection
count to in-flight requests instead of a budget — the opposite of what these
services need against a shared `max_connections` of 100.

Until then: stay on `pdo_pgsql`, accept that concurrency is `worker_num`, and
size `poolSize` for what one worker holds at once (see section 9).

### Why it was written

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

### If the client is ever fixed

Set `driverClass: \Spatial\Entity\Driver\PgSQL\Driver` in the `doctrine.yaml`
connection block, then re-check the budget: each worker can hold `poolSize`
concurrent connections rather than roughly one, so the ceiling per service
becomes `worker_num x pools x poolSize`. Lower `worker_num` as effective
concurrency rises, and confirm the total against `max_connections`.

Run the conformance and benchmark suites in that container first — the first
exercises parameterised queries, transactions, SQLSTATE mapping, concurrency and
exhaustion; the second is what surfaces a pool that is silently reconnecting:

```bash
php vendor/spatial/doctrine/tests/driver-conformance.php
php vendor/spatial/doctrine/tests/driver-benchmark.php
```

Two further limitations: only positional `?` placeholders are rewritten to
`$1, $2`, so named parameters in raw DBAL SQL are unsupported (no service uses
them today), and binary or large-object parameters are rejected rather than
silently corrupted.

[opsway]: https://github.com/opsway/doctrine-dbal-swoole-pgsql-driver

## 9. Size `poolSize` against the shared server, not per service

The four services on `host.docker.internal:5432` share one Postgres with
`max_connections = 100`, three of which are superuser-reserved. Each service
pins `worker_num x poolSize` connections, because `warmup()` fills the pool in
every worker.

At `worker_num: 8` and `poolSize: 8` that is 64 connections for one service.
`nx_api` was measured holding 64 of the 100 while `nx_suite_api`,
`nx_identity_api` and `nx_intelligence_api` had yet to warm theirs; had all four
warmed at those settings they would have asked for roughly 208. The others are
lazy today only because nothing constructs their `DbConnection` subclasses
during boot, so `warmup()` finds no pool to fill — a timing accident, not a
safety margin.

Those connections could not have been used in any case. `pdo_pgsql` blocks its
worker, so a worker runs one query at a time whatever `poolSize` says; the rest
of the pool is open connections that no code path can reach. Size it for what a
worker genuinely holds at once — the EntityManager running the query, plus slack
for coroutines holding one across a Redis or HTTP call.

`nx_api`, `nx_suite_api`, and `nx_identity_api` now run `worker_num: 8`,
`poolSize: 3` (24 connections each). `nx_intelligence_api` uses `poolSize: 2`.
`nx_notify` uses its own database at `poolSize: 3`.

## Still outstanding

Tracked for follow-up phases:

### Done since this doc was first written

- OTel Phase 1: coroutine-safe `ContextStorage`, W3C inbound/outbound propagation,
  Guzzle outbound tracing, DBAL query spans (`spatial/core` + `spatial/doctrine`
  v4.2.x).
- Shared-DB `poolSize` reduced to `3` on `nx_api`, `nx_suite_api`, and
  `nx_identity_api`; `nx_intelligence_api` uses `poolSize: 2` via `DbConnection`.
- `nx_api` PSR-4 fixes: `Control/Interverse/`, middleware filenames,
  `RedisCacheService` namespace, `phpstan-stubs.php` excluded from classmap.
- `nx_notify` V2 providers and domain API migration via `Spatial\Notify\NotifyGateway`.
- Doctrine Redis metadata/query/result caches (`spatial/doctrine` v4.2.5) gated by
  `APP_ENV` → `enableProdMode`.
- OTel sampler from `OTEL_TRACES_SAMPLER` / `_ARG`; dual autoload env vars removed
  from `.env.example`; queue producer/consumer spans + W3C on AMQP headers.
- Dead `DoctrineConfig` class + old `Connection\ConnectionPool` removed.

### Remaining

- **Coroutine PostgreSQL driver** — do not enable until OpenSwoole fixes
  cross-coroutine connection reuse (section 8).
- **`nx_notify` email templates** — identity + suite password/welcome/verification
  use versioned templates; remaining callers (`nx_api` featured/relationship,
  suite entity create/update) still use `transactional.raw`.
- **Legacy v1** `POST /notify-api/send` — retire once all callers use V2.
- **Remaining nx_api PSR-4** — audit any classes still outside PSR-4.
- Prepared-statement caching in the coroutine driver — moot while that driver is
  disabled.
- **PgBouncer** — optional capacity lever (see section 10); not required after
  Phase 0 budget cuts.

## 10. PgBouncer (optional)

PgBouncer sits between services and PostgreSQL and multiplexes many client
connections onto fewer real backends:

```
services (worker_num × pools × poolSize clients)
        ↓
    PgBouncer
        ↓
 PostgreSQL (smaller max_connections budget)
```

**When to add it.** After Phase 0, direct connections are sized for the shared
host. Introduce PgBouncer when you need more workers/services without raising
Postgres `max_connections`, or when `pg_stat_activity` shows the budget is
tight again.

**Mode.** Prefer **transaction** pooling for short request-scoped work. Avoid
**statement** pooling with Doctrine/`pdo_pgsql` prepared statements. If you rely
on session state (temp tables, `SET`, advisory locks held across round-trips),
use **session** pooling instead and size backends closer to client count.

**What it does not replace.** Pool-leak fixes, `poolSize` budgets, and worker
hooks remain mandatory. PgBouncer only reduces the Postgres-side connection
count.

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
