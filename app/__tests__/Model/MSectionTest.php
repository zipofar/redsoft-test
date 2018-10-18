<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Zipofar\Model\MSection;
use Zipofar\Database\ZPdo;

class MSectionTest extends TestCase
{
    use TestCaseTrait;

    private $section;
    private $pdo;

    public function setUp()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__.'/..');
        $dotenv->safeLoad();

        $app = new \Zipofar\App();
        $container = $app->getContainer();
        $this->section = $container->get(MSection::class);
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
        $id = '1';
        $expected = [
            'id' => $id,
            'name' => 'Food',
        ];
        $res = $this->section->getById($id);
        $this->assertEquals($expected, $res);
    }

    public function test_getHierarchy()
    {
        $expected = ['id' => '15', 'name' => 'Sweet', 'level' => '3'];

        $res = $this->section->getHierarchy();
        $this->assertCount(15, $res);
        $this->assertEquals($expected, $res[14]);
    }

    public function test_addSection()
    {
        $expected = ['id' => '16', 'name' => 'Bitter', 'level' => '3'];
        $params = [
            'parent_id' => 10,
            'name' => 'Bitter'
        ];
        $this->section->addSection($params);

        $res = $this->section->getHierarchy();
        $this->assertCount(16, $res);
        $this->assertEquals($expected, $res[10]);
    }

    public function test_updateSection()
    {
        $id = '9';
        $expected = ['id' => $id, 'name' => 'FruitUpdated', 'level' => '1'];
        $params = [
            'id' => $id,
            'name' => 'FruitUpdated'
        ];
        $this->section->updateSection($params);

        $res = $this->section->getHierarchy();
        $this->assertEquals($expected, $res[8]);
    }

    public function test_deleteSection()
    {
        $id = '9';
        $expected = ['id' => $id, 'name' => 'FruitUpdated', 'level' => '1'];

        $this->section->deleteSection($id);

        //Check isset products after it was must be deleted
        $sql = 'SELECT * FROM product WHERE id BETWEEN 5 AND 8';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        $this->assertEquals([], $res);

        //Check isset sections after it was must be deleted
        $sql = 'SELECT * FROM section WHERE id BETWEEN 9 AND 15';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        $this->assertEquals([], $res);

        //Check isset productsection after it was must be deleted
        $sql = 'SELECT * FROM productsection WHERE id BETWEEN 5 AND 8';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();
        $this->assertEquals([], $res);
    }

}
