<?php

require_once "../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;
use Zipofar\Core;

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$app = new Core();
$request = Request::createFromGlobals();

$app->addRoute('/api/id/{id}', 'Product', 'getById');
$app->addRoute('/api/product_name/{name}/{offset}', 'Product', 'getBySubStrName');
$app->addRoute('/api/brand/{name}/{offset}', 'Product', 'getByBrand');
$app->addRoute('/api/section/{name}/{offset}', 'Product', 'getBySection');
$app->addRoute('/api/sections/{name}/{offset}', 'Product', 'getBySections');

$response = $app->handle($request);
$response->send();





