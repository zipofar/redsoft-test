<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Zipofar\Model\MProduct;
use Zipofar\Database\ZPdo;

class MProductTest extends TestCase
{
    use TestCaseTrait;

    private $product;
    private $pdo;

    public function setUp()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__.'/..');
        $dotenv->safeLoad();

        $app = new \Zipofar\App();
        $container = $app->getContainer();
        $this->product = $container->get(MProduct::class);
        $this->pdo = $container->get(ZPdo::class)->getPDO();

        parent::setUp();

        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    public function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, $_ENV['DB_NAME']);
    }

    public function getDataSet()
    {
        $pathDataSet = __DIR__.'/../__fixtures__/testdata.xml';
        return $this->createFlatXMLDataSet($pathDataSet);
    }

    public function test_getById()
    {
        $id = 1;
        $expected = [
            'id' => '1',
            'name' => 'FoodVegRedSour',
            'availability' => '1',
            'price' => '1.99',
            'brand' => 'Russia Kolhoz'
        ];
        $res = $this->product->getById($id);
        $this->assertEquals($expected, $res);
    }

    public function test_getProducts_AllFields()
    {
        $params = [
            'name' => 'FoodVegRedSour',
            'availability' => '1',
            'price' => '1.99',
            'brand' => 'Russia Kolhoz'
        ];

        $expected = [
            [
            'id' => '1',
            'name' => 'FoodVegRedSour',
            'availability' => '1',
            'price' => '1.99',
            'brand' => 'Russia Kolhoz'
            ]
        ];

        $this->assertEquals($expected, $this->product->getProducts($params));
    }

    public function test_getProducts_OnlyPageAndPerPage()
    {
        $params = [
            'page' => '1',
            'per_page' => '1'
        ];

        $expected = [
            [
                'id' => '1',
                'name' => 'FoodVegRedSour',
                'availability' => '1',
                'price' => '1.99',
                'brand' => 'Russia Kolhoz'
            ]
        ];

        $this->assertEquals($expected, $this->product->getProducts($params));
    }

    public function test_getProducts_ClauseLIKE()
    {
        $params = ['name' => '%Veg%'];

        $expected = [
            [
                'id' => '1',
                'name' => 'FoodVegRedSour',
                'availability' => '1',
                'price' => '1.99',
                'brand' => 'Russia Kolhoz'
            ],
            [
                'id' => '2',
                'name' => 'FoodVegRedSweet',
                'availability' => '1',
                'price' => '1.50',
                'brand' => 'Gruzin'
            ],
            [
                'id' => '3',
                'name' => 'FoodVegGreenSour',
                'availability' => '1',
                'price' => '1.50',
                'brand' => 'Russia Kolhoz'
            ],
            [
                'id' => '4',
                'name' => 'FoodVegGreenSweet',
                'availability' => '1',
                'price' => '1.50',
                'brand' => 'Country'
            ],
            [
                'id' => '9',
                'name' => 'FoodVegRedSour2',
                'availability' => '1',
                'price' => '1.99',
                'brand' => 'Russia Kolhoz'
            ],
        ];

        $this->assertEquals($expected, $this->product->getProducts($params));
    }

    public function test_getProducts_MultyBrand()
    {
        $params = ['brand' => 'Dacha|Polsky'];

        $expected = [
            [
                'id' => '5',
                'name' => 'FoodFruitRedSour',
                'availability' => '1',
                'price' => '1.50',
                'brand' => 'Dacha'
            ],
            [
                'id' => '6',
                'name' => 'FoodFruitRedSweet',
                'availability' => '1',
                'price' => '1.50',
                'brand' => 'Polsky'
            ],
        ];

        $this->assertEquals($expected, $this->product->getProducts($params));
    }

    public function test_getProductsInSection_AllProductsInSection()
    {
        $id = 4;
        $params = [];

        $expected = [
            [
                'id' => '1',
                'name' => 'FoodVegRedSour',
                'availability' => '1',
                'price' => '1.99',
                'brand' => 'Russia Kolhoz'
            ],
            [
                'id' => '9',
                'name' => 'FoodVegRedSour2',
                'availability' => '1',
                'price' => '1.99',
                'brand' => 'Russia Kolhoz'
            ],
        ];

        $this->assertEquals($expected, $this->product->getProductsInSection($id, $params));
    }

    public function test_getProductsInSection_FilteredProductsInSection()
    {
        $id = 4;
        $params = ['name' => 'FoodVegRedSour'];

        $expected = [
            [
                'id' => '1',
                'name' => 'FoodVegRedSour',
                'availability' => '1',
                'price' => '1.99',
                'brand' => 'Russia Kolhoz'
            ],
        ];

        $this->assertEquals($expected, $this->product->getProductsInSection($id, $params));
    }
}
