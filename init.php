<?php
require_once("vendor/autoload.php");
require_once('app/cores/Websocket.php');

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MM\cores\ChatWebSocket\Chat;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    9090
);
try {
    $server->run();
} catch (Exception $e) {
    echo 'Error has occurred';
    exit();
}
?>