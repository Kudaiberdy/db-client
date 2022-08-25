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
    private $dbConnection;
    private $cache;

    public function __construct(string $pathToConf)
    {
        $conf = parse_ini_file($pathToConf);
        $host = $conf['host'];
        $port = $conf['port'];
        $user = $conf['user'];
        $password = $conf['password'];
        parent::__construct($host, $port, $user, $password);
    }

    public function declareConnection(string $exchange, string $queue, string $routingKey)
    {
        $this->exchange = $exchange;
        $this->queue = $queue;
        $this->routingKey = $routingKey;
        $this->channel = $this->channel();

        $this->channel->exchange_declare($exchange, 'direct');
        $this->channel->queue_declare(
            $queue,
            false,
            true,
            false
        );
        $this->channel->queue_bind($queue, $exchange, $routingKey);
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
            $this->channel->wait();
        }
    }

    public function addDBConnection(&$dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function addCache(&$cache)
    {
        $this->cache = $cache;
    }

    public function messageProcessing(AMQPMessage $message)
    {
        $jsonData = json_decode($message->getBody(), true);
        $this->dbConnection->insert($jsonData);
        $this->cache->flush();
        $this->dbConnection->dumpEmailsToCache($this->cache);

        $message->ack();
    }
}
