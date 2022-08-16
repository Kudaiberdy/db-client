<?php

namespace App\Bootstrap;
function run()
{
    $connection = new \App\AMQPConsumers\Consumer();

    $connection->listen();
}
