<?php

require_once "../vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(getcwd()."/../");
$dotenv->load();

function createTables($tables, $pdo)
{
    foreach ($tables as $tableName => $query) {
        $pdo->exec("SET foreign_key_checks = 0");
        $pdo->exec("DROP TABLE IF EXISTS $tableName");
        $pdo->exec("SET foreign_key_checks = 1");
        $pdo->exec($query);
    }
}

$pdo = Zipofar\Db::getInstance();
$tables = require_once "Tables.php";

createTables($tables, $pdo);
