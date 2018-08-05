<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class MProductTest extends TestCase
{
    use TestCaseTrait;

    public function getConnection()
    {
        $dotenv = new Dotenv\Dotenv(getcwd()."/__tests__/");
        $dotenv->load();

        $pdo = Zipofar\Db::getInstance();

        return $this->createDefaultDBConnection($pdo, $_ENV['DB_NAME']);
    }

    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/__fixtures__/guestbook.xml');
    }


    public function testCalculate()
    {
        $this->assertEquals(2, 1 + 1);
    }
}