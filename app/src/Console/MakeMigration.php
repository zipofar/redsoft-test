<?php

namespace Zipofar\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zipofar\Database\Migration;

class MakeMigration extends Command
{
    protected function configure()
    {
        $this
            ->setName('make:migration')
            ->setDescription('This command create tables in database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tables = require_once dirname(__DIR__).'/Database/Tables.php';

        $app = new \Zipofar\App();
        $container = $app->getContainer();
        $pdo = $container->get(\Zipofar\Database\ZPdo::class)->getPDO();

        foreach ($tables as $tableName => $query) {
            $pdo->exec("SET foreign_key_checks = 0");
            $pdo->exec("DROP TABLE IF EXISTS $tableName");
            $pdo->exec("SET foreign_key_checks = 1");
            $pdo->exec($query);
        }

        $output->writeln(['Migration is DONE']);
    }
}
