<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Connections\DBConnection;

class UserController
{
    public static function index(Request $request, Response $response)
    {
        [$key, $value] = explode('=', $request->getUri()->getQuery());

        $cache = new \Memcached();
        $cache->addServer(...parse_ini_file(__DIR__ . '/../../configs/memcachedconnection.ini'));
        $dbConnection = new DBConnection(__DIR__ . '/../../configs/dbconnection.ini');

        $cacheKey = "{$key}:{$value}";
        $result = json_encode($dbConnection->index($key, $value));
        $cache->add($cacheKey, $result);
        $response->getBody()->write($result);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
