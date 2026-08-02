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
use OpenSwoole\Process;
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
 * Worker count is set explicitly.
 *
 * OpenSwoole otherwise defaults to one worker per CPU core, which silently
 * multiplies the database connection footprint by the size of the host:
 * total connections = workers x pools per service x poolSize. Keep this in
 * step with `poolSize` in config/packages/doctrine.yaml and with the
 * database server's own connection limit.
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
 * Graceful shutdown. Process::signal is used rather than pcntl_signal, which
 * needs an explicit pcntl_signal_dispatch() pump that an event loop never runs.
 */
Process::signal(SIGTERM, function () use ($http) {
    echo "Received SIGTERM, draining workers...\n";
    $http->shutdown();
});

$http->start();
