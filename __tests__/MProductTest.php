<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Zipofar\Database\Migration;
use Zipofar\Model\MProduct;

class MProductTest extends TestCase
{
    use TestCaseTrait;

    private $product;

    public function getConnection()
    {
        $dotenv = new Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        $pdo = \Zipofar\Database\Db::getInstance();

        return $this->createDefaultDBConnection($pdo, $_ENV['DB_NAME']);
    }

    public function getDataSet()
    {
        $this->product = new MProduct();

        return $this->createFlatXMLDataSet(dirname(__FILE__).'/__fixtures__/testdata.xml');
    }

    public function testGetById()
    {
        $expected = [
            'id' => '1',
            'name' => 'FoodVegRedSour',
            'availability' => '1',
            'price' => '1.99',
            'brand' => 'Russia Kolhoz'
        ];
        
        $this->assertEquals($expected, $this->product->getById(1));

        $this->assertEquals([], $this->product->getById(1000));
    }

    public function testGetBySubStrName()
    {
        $expectedFirstItem = [
            "id" => "8",
            "name" => "FoodFruitGreenSweet",
            "availability" => "1",
            "price" => "1.50",
            "brand" => "Petrovna"
        ];

        $products = $this->product->getBySubStrName('sweet');

        $this->assertEquals($expectedFirstItem, $products[0]);
        $this->assertEquals(4, count($products));
    }

    public function testGetByBrand()
    {
        $firstBrand = [
            "id" => "5",
            "name" => "FoodFruitRedSour",
            "availability" => "1",
            "price" => "1.50",
            "brand" => "Dacha"
        ];

        $secondBrand = [
            "id" => "4",
            "name" => "FoodVegGreenSweet",
            "availability" => "1",
            "price" => "1.50",
            "brand" => "Country"
        ];

        $singleBrand[0] = $firstBrand;

        $multyBrand[0] = $firstBrand;
        $multyBrand[1] = $secondBrand;

        $this->assertEquals($singleBrand, $this->product->getByBrand("dacha"));
        $this->assertEquals($multyBrand, $this->product->getByBrand("dacha+country"));
    }

    public function testGetBySection()
    {
    
    }

    public function testGetBySections()
    {
    
    }
}
