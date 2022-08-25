<?php

namespace App\Bootstrap;

use App\Connections\AMQPConnection;
use App\Connections\DBConnection;

function run()
{
    $amqpConnection = new AMQPConnection(__DIR__ . '/../configs/amqpconnection.ini');
    $amqpConnection->declareConnection('router', 'push-queue', 'push');

    $dbConnection = new DBConnection(__DIR__ . '/../configs/dbconnection.ini');

    $cache = new \Memcached();
    $cache->addServer(...parse_ini_file(__DIR__ . '/../configs/memcachedconnection.ini'));
    $cache->delete('emails');

    $amqpConnection->addCache($cache);
    $amqpConnection->addDBConnetions($dbConnection);
    $dbConnection->dumpEmailsToCache($cache);

    $amqpConnection->listen($dbConnection, $cache);
}
