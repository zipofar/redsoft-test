<?php

namespace Zipofar;

class Db
{
    private static $instance = null;

    private function __construct()
    {
        $dsn = $_ENV['DB_TYPE'].":host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_NAME'].";charset=utf8";
        $opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];
        $pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $opt);
        self::$instance = $pdo;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            new self();
        }
        return self::$instance;
    }

    private function __clone()
    {
    }
    private function __wakeup()
    {
    }
}
