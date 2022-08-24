<?php

namespace App\DBConnections;

class Connection extends \PDO
{
    public function __construct(string $pathToConf)
    {
        $config = parse_ini_file($pathToConf);
        $server = $config['server'];
        $port = $config['port'];
        $dbname = $config['dbname'];
        $user = $config['user'];
        $password = $config['password'];

        $dsn = "mysql:host={$server};port={$port};dbname={$dbname}";

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
            $this->query($statment)->execute();
        } catch (\PDOException $e) {
            return $e;
        }
    }

    public function index(string $key, string $value)
    {
        $statment = "SELECT * FROM users WHERE $key LIKE '%$value%'";

        try {
            $result = $this->query($statment)->fetchAll(self::FETCH_ASSOC);
            return json_encode($result);
        } catch (\PDOException $e) {
            return $e;
        }
    }
}
