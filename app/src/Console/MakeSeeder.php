<?php

namespace Zipofar\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zipofar\Database\Db;
use Zipofar\Database\TestDataLoader;

class MakeSeeder extends Command
{
    protected function configure()
    {
        $this
            ->setName('make:seeder')
            ->setDescription('This command create records in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = require_once __DIR__.'/../Database/testdata.php';
        $pdo = Db::getInstance();
        
        $migration = new TestDataLoader($pdo, $data);
        $migration->insertData();
    }
}
