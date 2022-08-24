<?php

namespace App\AMQPConsumers;

use App\DBConnections\Connection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    public $channel;

    public function __construct()
    {
        $connection = new AMQPStreamConnection(
            'localhost',
            5672,
            'guest',
            'guest'
        );

        $this->channel = $connection->channel();
        $this->channel->exchange_declare('router', 'direct');
        $this->channel->queue_declare(
            'push-queue',
            false,
            true,
            false
        );
        $this->channel->queue_bind(
            'push-queue',
            'router',
            'push'
        );
    }

    public function listen()
    {
        $this->channel->basic_consume(
            'push-queue',
            '',
            false,
            false,
            false,
            false,
            [$this, 'processMessage']
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    public function processMessage(AMQPMessage $message)
    {
        $jsonData = json_decode($message->getBody(), true);

        $connection = new Connection(__DIR__ . '/../../configs/dbconnect.ini');
        $connection->insert($jsonData);

        $message->ack();
    }
}
