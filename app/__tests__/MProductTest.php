<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
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
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/__fixtures__/testdata.xml');
    }

    public function testGetById()
    {
        $product = new MProduct();

        $expected = [
            'id' => '1',
            'name' => 'FoodVegRedSour',
            'availability' => '1',
            'price' => '1.99',
            'brand' => 'Russia Kolhoz'
        ];
        
        $this->assertEquals($expected, $product->getById(1));

        $this->assertEquals([], $product->getById(1000));
    }

    public function testGetBySubStrName()
    {
        $product = new MProduct();

        $expectedFirstItem = [
            "id" => "8",
            "name" => "FoodFruitGreenSweet",
            "availability" => "1",
            "price" => "1.50",
            "brand" => "Petrovna"
        ];

        $products = $product->getBySubStrName('sweet');

        $this->assertEquals($expectedFirstItem, $products[0]);
        $this->assertEquals(4, count($products));

        // Enable OFFSET = 1
        $products = $product->getBySubStrName('sweet', 1);
        $this->assertEquals(3, count($products));

        // Enable LIMIT
        $product2 = new MProduct(['limit' => 1]);
        $products = $product2->getBySubStrName('sweet');

        $this->assertEquals(1, count($products));
    }

    public function testGetByBrand()
    {
        $product = new MProduct();

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

        $this->assertEquals($singleBrand, $product->getByBrand("dacha"));
        $this->assertEquals($multyBrand, $product->getByBrand("dacha+country"));

        // Enable OFFSET = 1
        $res1 = $product->getByBrand("Russia Kolhoz", 1);
        $this->assertEquals(1, count($res1));

        // Enable LIMIT
        $product2 = new MProduct(['limit' => 1]);
        $res2 = $product2->getByBrand("dacha+country");
        $this->assertEquals(1, count($res2));
    }

    public function testGetBySection()
    {
        $product = new MProduct();
        $expected = [
            0 => ["id" => "5", "name" => "FoodFruitRedSour", "availability" =>"1", "price" => "1.50", "brand" => "Dacha"],
            1 => ["id" => "6", "name" => "FoodFruitRedSweet", "availability" => "1", "price" => "1.50", "brand" => "Polsky"],
            2 => ["id" => "1", "name" => "FoodVegRedSour", "availability" => "1", "price" => "1.99", "brand" => "Russia Kolhoz"],
            3 => ["id" => "2", "name" => "FoodVegRedSweet", "availability" => "1", "price" => "1.50", "brand" => "Gruzin"]
        ];

        $this->assertEquals($expected, $product->getBySection('red'));

        // Enable OFFSET = 1
        $res1 = $product->getBySection('red', 1);
        $this->assertEquals(3, count($res1));

        // Enable LIMIT
        $product2 = new MProduct(['limit' => 1]);
        $this->assertEquals([$expected[0]], $product2->getBySection('red'));
    }

    public function testGetBySections()
    {
        $product = new MProduct();

        $expected1 = [
            0 => ["id" => "6", "name" => "FoodFruitRedSweet", "availability" => "1", "price" => "1.50", "brand" => "Polsky"]
        ];

        $this->assertEquals($expected1, $product->getBySections('food>>fruit>>red>>sweet'));

        $res1 = $product->getBySections('food>>fruit>>red');
        $this->assertEquals(2, count($res1));

        $res2 = $product->getBySections('food');
        $this->assertEquals(8, count($res2));

        // Enable OFFSET = 1
        $res3 = $product->getBySections('food>>fruit>>red', 1);
        $this->assertEquals(1, count($res3));

        // Enable LIMIT
        $product2 = new MProduct(['limit' => 1]);
        $res4 = $product2->getBySections('food>>fruit');
        $this->assertEquals(1, count($res4));
    }
}
