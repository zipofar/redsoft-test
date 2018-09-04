<?php

namespace Zipofar\Database;

use PHPUnit\DbUnit\TestCaseTrait;

class TestDataLoader
{
    protected $pdo;
    protected $data;

    public function __construct(\PDO $pdo, array $data)
    {
        $this->pdo = $pdo;
        $this->data = $data;
    }

    public function insertData()
    {
        foreach ($this->data as $tableName => $rows) {

            if (empty(end($rows))) {
                continue;
            }

            $columnsName = implode(', ', array_keys(end($rows)));
            $placeholders = implode(', ', array_fill(0, count(end($rows)), '?'));
            $sql = "INSERT INTO $tableName ($columnsName) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($rows as $row) {
                $stmt->execute(array_values($row));
            }
        }
    }
}
