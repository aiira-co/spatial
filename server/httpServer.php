<?php

use Spatial\App;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

return function (App $app) {
    /**
     * We Initiate Bridge Manager
     */
    $bridgeManager = new BridgeManager($app);

    /**
     * Start Swoole Server
     */
    $server = new Server("0.0.0.0", 8282);

    /**
     * Register the on 'start' event
     */
    $server->on(
        'start',
        function (Server $server) {
            echo "Swoole Websocket Server is started at http://{$server->host}:{$server->port}\n";
        }
    );

    /**
     * Register the on 'request' event, which will use the bridgeManager to transform request, process it
     * as a Spatial request and merge back the response
     */
    $server->on(
        'request',
        function (Request $request, Response $response) use ($bridgeManager) {
            $bridgeManager->process($request, $response)->end();
        }
    );


    $server->start();
};


