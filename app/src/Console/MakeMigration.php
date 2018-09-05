<?php

namespace Zipofar\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Zipofar\Database\Db;
use Zipofar\Database\Migration;

class MakeMigration extends Command
{
    protected function configure()
    {
        $this
            ->setName('make:migration')
            ->setDescription('This command create tables in database')
            ->setHelp('Add argument "testdb" for create tables in test database')
            ->addArgument('testdb', InputArgument::OPTIONAL, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('testdb')) {
            $pathToTestsDir = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'__tests__';
            $dotenv = new \Dotenv\Dotenv($pathToTestsDir);
            $dotenv->overload();
        }
        
        $tables = require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'Database'.DIRECTORY_SEPARATOR.'Tables.php';
        $pdo = Db::getInstance();
        
        $migration = new Migration($pdo, $tables);
        $migration->createTables();

        $output->writeln(['Process migration']);
    }
}
