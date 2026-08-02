<?php

/**
 * Copyright (c) 2021 Aiira Inc.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     [ aiira ]
 * @subpackage  [ spatial ]
 * @author      Owusu-Afriyie Kofi <koathecedi@gmail.com>
 * @copyright   2021 Aiira Inc.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://aiira.co
 * @version     @@4.00@@
 */

declare(strict_types=1);

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
use OpenSwoole\Runtime;
use Presentation\AppModule;
use Spatial\Core\App;
use Spatial\Entity\DbConnection;
use Spatial\Swoole\BridgeManager;
use Spatial\Telemetry\OtelProviderFactory;

const DS = DIRECTORY_SEPARATOR;
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

Runtime::enableCoroutine(true, Runtime::HOOK_ALL);

/**
 * Bootstrap the application in the master process.
 *
 * Everything built here is inherited by every worker through fork, so it must
 * stay limited to configuration, routing tables and the DI container. Database
 * connection pools are created empty on purpose and populated per worker in
 * onWorkerStart below — see DbConnection::warmup().
 */
$app = new App();

co::run(function () use ($app) {
    $app->boot(AppModule::class);
});

$bridgeManager = new BridgeManager($app);

$http = new Server("0.0.0.0", 8080);

/**
 * Worker count is set explicitly, because it caps both throughput and the
 * database footprint. OpenSwoole otherwise defaults to one worker per CPU
 * core, which sizes your connection usage to whatever host you deploy on.
 *
 * How many connections that means depends on the driver. OpenSwoole exposes no
 * PDO PostgreSQL coroutine hook, so with a plain `pdo_pgsql` driver a query
 * blocks its whole worker: concurrency comes from workers rather than
 * coroutines, a worker holds about one connection, and the pool never fills.
 * Uncomment `driverClass` in config/packages/doctrine.yaml to get the OpsWay
 * coroutine driver, and queries then genuinely run in parallel — at which
 * point the ceiling becomes `worker_num x poolSize` per pool.
 *
 * Budget across every service sharing the database, not just this one.
 */
$http->set([
    'worker_num' => (int)(getenv('SWOOLE_WORKER_NUM') ?: 4),
    // Workers are not recycled: each one owns a connection pool, and
    // recycling would churn database connections for no benefit here.
    'max_request' => 0,
    'enable_coroutine' => true,
]);

$http->on(
    "start",
    function (Server $server) {
        echo sprintf('Swoole http server is started at http://%s:%s', $server->host, $server->port), PHP_EOL;
    }
);

/**
 * Per-worker setup, after the fork.
 */
$http->on(
    "workerStart",
    function (Server $server, int $workerId) {
        // Populate this worker's own pools. Creating an EntityManager does not
        // open a socket — DBAL connects lazily on first query — so this is
        // cheap and keeps checkout latency predictable.
        try {
            DbConnection::warmup();
        } catch (Throwable $e) {
            // Fall back to on-demand creation inside the request coroutine.
            error_log("Worker {$workerId} pool warmup skipped: " . $e->getMessage());
        }

        // ExportingReader has no internal schedule, so without this tick
        // metrics would only be exported when the process finally exits.
        OtelProviderFactory::registerFlushTimer();
    }
);

/**
 * Per-worker teardown. Runs on reload and on shutdown, unlike
 * register_shutdown_function, which only fires when the process itself exits.
 */
$http->on(
    "workerStop",
    function (Server $server, int $workerId) {
        DbConnection::closeAllConnection();
        OtelProviderFactory::shutdown();
    }
);

$http->on(
    "request",
    function (Request $request, Response $response) use ($bridgeManager, $http) {
        $bridgeManager->process($request, $response, $http)->end();
    }
);

/**
 * SIGTERM needs no handler here. The OpenSwoole master installs its own, which
 * shuts the server down gracefully and so fires workerStop, draining pools and
 * flushing telemetry.
 *
 * Do not call Process::signal() at this level. Before start() it creates the
 * event loop, and Server::start() then refuses with "eventLoop has already been
 * created", leaving supervisor to restart-loop into a FATAL state.
 */

$http->start();
