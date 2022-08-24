<?php

namespace App\Bootstrap;

use App\Connections\AMQPConnection;

function run()
{
    $connection = new AMQPConnection(__DIR__ . '/../configs/amqpconnection.ini');
    $connection->declareConnection('router', 'push-queue', 'push');
    $connection->listen();
}
