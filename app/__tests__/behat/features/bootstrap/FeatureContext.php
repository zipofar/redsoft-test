<?php

namespace ZipofarBehatFeatureContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Imbo\BehatApiExtension\Context\ApiContext;
use Behat\Behat\Hook\Scope\BeforeFeatureScope;
use PHPUnit\DbUnit\TestCaseTrait;
use Zipofar\Database\ZPdo;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends ApiContext implements Context
{
    use TestCaseTrait;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through Behat.yml.
     */
    public function __construct()
    {
    }

    /** @BeforeScenario */
    public function resetDatabase()
    {
        $app = new \Zipofar\App();
        $container = $app->getContainer();
        $this->pdo = $container->get(ZPdo::class)->getPDO();
        $this->databaseTester = null;
        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();
    }

    protected function getConnection()
    {
        return $this->createDefaultDBConnection($this->pdo, $_ENV['DB_NAME']);
    }

    protected function getDataSet()
    {
        $pathDataSet = __DIR__.'/../../../__fixtures__/testdata.xml';
        return $this->createFlatXMLDataSet($pathDataSet);
    }
}
