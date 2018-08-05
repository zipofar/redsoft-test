<?php

require_once __DIR__."/vendor/autoload.php";

use Symfony\Component\Console\Application;
use \Zipofar\Console\MakeMigration;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$application = new Application();

$application->add(new MakeMigration());

$application->run();
