<?php

namespace Zipofar\Database;

class Migration
{
    protected $pdo;
    protected $tables;

    public function __construct(\PDO $pdo, array $tables)
    {
        $this->pdo = $pdo;
        $this->tables = $tables;
    }

    public function createTables()
    {
        foreach ($this->tables as $tableName => $query) {
            $this->pdo->exec("SET foreign_key_checks = 0");
            $this->pdo->exec("DROP TABLE IF EXISTS $tableName");
            $this->pdo->exec("SET foreign_key_checks = 1");
            $this->pdo->exec($query);
        }
    }
}
