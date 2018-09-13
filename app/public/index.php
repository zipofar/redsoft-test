<?php

require_once "../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Response;
use Zipofar\Controller\Product;

$app = new Zipofar\App();

$app->get('/api/id/{id}', [Product::class, 'getById']);
$app->get('/api/product_name/{name}/{offset}', [Product::class, 'getBySubStrName'], ['offset' => 0]);
$app->get('/api/brand/{name}/{offset}', [Product::class, 'getByBrand'], ['offset' => 0]);
$app->get('/api/section/{name}/{offset}', [Product::class, 'getBySection'], ['offset' => 0]);
$app->get('/api/sections/{name}/{offset}', [Product::class, 'getBySections'], ['offset' => 0]);
$app->get('/api/hierarchy/{pretty}', [Product::class, 'getHierarchy'], ['pretty' => false]);


$app->get('/hello', [\Zipofar\Controller\Hello::class, 'index']);
$app->get('/bye', function ($request, $response) { return $response->setContent('BYE'); });
$app->get('/test', function () { return $this->get(Zipofar\Controller\Hello::class)->index(); });

$app->run();











