<?php

namespace App\DBConnections;

class Connection extends \PDO
{
    public function __construct()
    {
        $config = parse_ini_file(__DIR__ . '/../../dbconnect.ini');
        $server = $config['server'];
        $port = $config['port'];
        $dbname = $config['dbname'];
        $user = $config['user'];
        $password =$config['password'];

        $dsn = "mysql:host={$server};dbname={$dbname}";

        try {
            parent::__construct($dsn, $user, $password);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function insert($params)
    {
        $name = $params['name'];
        $phone = $params['phone'];
        $country = $params['country'];
        $region = $params['region'];
        $numberrange = $params['numberrange'];
        $email = $params['email'];

        $statment = "INSERT INTO users (name, phone, country, region, numberrange, email)
                VALUES ('{$name}', '{$phone}', '{$country}', '{$region}', '{$numberrange}', '{$email}')";
        try {
            $this->query($statment);
        } catch (\PDOException $e) {
            return $e;
        }
    }

    public function select($key, $value)
    {
        try {
            return $this->connect->query("SELECT * FROM users WHERE $key = '{$value}';")->fetchAll();
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function index()
    {
        return $this->connect->query("SELECT * FROM users;")->fetchAll();
    }

}
