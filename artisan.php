<?php

require_once "../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use Zipofar\Core;

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();