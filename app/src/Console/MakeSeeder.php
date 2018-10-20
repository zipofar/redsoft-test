<?php

namespace Zipofar\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PHPUnit\DbUnit\TestCaseTrait;

class MakeSeeder extends Command
{
    use TestCaseTrait;

    private $pdo;

    protected function configure()
    {
        $this
            ->setName('make:seeder')
            ->setDescription('This command create records in database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = new \Zipofar\App();
        $container = $app->getContainer();
        $this->pdo = $container->get(\Zipofar\Database\ZPdo::class)->getPDO();
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
        $pathDataSet = __DIR__.'/../../__tests__/__fixtures__/testdata.xml';
        return $this->createFlatXMLDataSet($pathDataSet);
    }
}
