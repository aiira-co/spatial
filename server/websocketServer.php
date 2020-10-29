<?php

use Spatial\App;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

return function (App $app) {
    /**
     * Start Swoole Server
     */
    $server = new Server("0.0.0.0", 9502);

    /**
     * Register the on 'start' event
     */
    $server->on(
        'start',
        function (Server $server) use ($app) {
            echo "Swoole Websocket Server is started at http://{$server->host}:{$server->port}\n";
        }
    );

    /**
     * Register the on 'open' event, which will use the bridgeManager to transform request, process it
     * as a Spatial request and merge back the response
     */
    $server->on(
        'open',
        function (Server $server, Request $request) {
            echo "connection open: {$request->fd}\n";

            $server->tick(
                1000,
                function () use ($server, $request) {
                    $server->push($request->fd, json_encode(["hello", time()], JSON_THROW_ON_ERROR));
                }
            );

//            $message = (new Todo($app->getContainer()->dataDriver))->get();
//
//            foreach ($server->connections as $fd) {
//                $server->push($fd, json_encode($message));
//            }
        }
    );


    $server->on(
        'message',
        function (Server $server, Frame $frame) {
            echo "recieved message: {$frame->data}\n";

            $server->push($frame->fd, json_encode(["hello", time()], JSON_THROW_ON_ERROR));

//            invoke the request
//            $message = (new Todo($app->getContainer()->dataDriver))->get();
//
//            foreach ($server->connections as $fd) {
//                $server->push($fd, json_encode($message));
//        }
        }
    );


    $server->on(
        'close',
        function (Server $server, int $fd) use ($app) {
            echo "connection close: {$fd}\n";
        }
    );

    $server->start();
};


