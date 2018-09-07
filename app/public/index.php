<?php

require_once "../vendor/autoload.php";

$app = new Zipofar\Core(new \DI\Container());

$app->addRoute('/api/id/{id}', 'Product', 'getById');
$app->addRoute('/api/product_name/{name}/{offset}', 'Product', 'getBySubStrName');
$app->addRoute('/api/brand/{name}/{offset}', 'Product', 'getByBrand');
$app->addRoute('/api/section/{name}/{offset}', 'Product', 'getBySection');
$app->addRoute('/api/sections/{name}/{offset}', 'Product', 'getBySections');
$app->addRoute('/api/hierarchy/{pretty}', 'Product', 'getHierarchy');

$app->run();





