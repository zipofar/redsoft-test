<?php

namespace Zipofar\Database;

use PHPUnit\DbUnit\TestCaseTrait;

class TestDataLoader
{
    private $pdo;
    private $data;

    public function __construct(\PDO $pdo, array $data)
    {
        $this->pdo = $pdo;
        $this->data = $data;
    }

    public function insertData()
    {
        foreach ($this->data as $tableName => $rows) {

var_dump($rows);
            if (!isset($rows[0])) {
                continue;
            }

            $columnsName = implode(', ', array_keys($rows[0]));
            $placeholders = implode(', ', array_fill(0, count($rows[0]), '?'));
            $sql = "INSERT INTO $tableName ($columnsName) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($rows as $row) {
                $stmt->execute(array_values($row));
            }
        }
    }
}
