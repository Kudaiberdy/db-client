<?php

namespace App\Bootstrap;

function run()
{
    $consumer = new \App\AMQPConsumers\Consumer();

    $consumer->listen();
}
