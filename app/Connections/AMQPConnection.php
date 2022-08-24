<?php

namespace App\Connections;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AMQPConnection extends AMQPStreamConnection
{
    private $exchange;
    private $queue;
    private $routingKey;
    private $channel;

    public function __construct($pathToConf)
    {
        $conf = parse_ini_file($pathToConf);
        $host = $conf['host'];
        $port = $conf['port'];
        $user = $conf['user'];
        $password = $conf['password'];
        parent::__construct($host, $port, $user, $password);
    }

    public function declareConnection($exchange, $queue, $routingKey)
    {
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->routingKey = $routingKey;
        $this->channel = $this->channel();

        $this->channel()->exchange_declare($exchange, 'direct');
        $this->channel()->queue_declare(
            $queue,
            false,
            true,
            false
        );
        $this->channel()->queue_bind($queue, $exchange, $routingKey);
    }

    public function listen()
    {
        $this->channel->basic_consume(
            $this->queue,
            '',
            false,
            false,
            false,
            false,
            [$this, 'messageProcessing']
        );

        while ($this->channel->is_consuming()) {
            $this->$this->channel();
        }
    }

    public function messageProcessing(AMQPMessage $message)
    {
        $jsonData = json_decode($message->getBody(), true);

        $connection = new DBConnection(__DIR__ . '/../../configs/dbconnection.ini');
        $connection->insert($jsonData);

        $message->ack();
    }
}
