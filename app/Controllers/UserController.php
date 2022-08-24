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
        $cacheKey = "{$key}:{$value}";
        $res = $cache->get($cacheKey);

        if ($res) {
            $response->getBody()->write($res);
            return $response
                ->withHeader('Content-Type', 'application/json');
        }

        $cache->flush();
        $dbconnection = new DBConnection(__DIR__ . '/../../configs/dbconnection.ini');
        $res = json_encode($dbconnection->index($key, $value));
        $cache->add($cacheKey, $res);
        $response->getBody()->write($res);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
