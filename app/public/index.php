<?php

require_once "../vendor/autoload.php";

use Zipofar\Controller\Product;

$app = new Zipofar\App();

$app->get('product.show', '/api/id/{id}', [Product::class, 'getById']);
$app->get('get_product_by_name', '/api/product_name/{name}/{offset}', [Product::class, 'getBySubStrName'], ['offset' => 0]);
$app->post('product.store', '/api/product_name', [Product::class, 'addProduct']);
$app->delete('product.destroy', '/api/product_name/{id}', [Product::class, 'deleteProduct']);
$app->put('product.update', '/api/product_name/{id}', [Product::class, 'putProduct']);
$app->get('get_product_by_brand', '/api/brand/{name}/{offset}', [Product::class, 'getByBrand'], ['offset' => 0]);
$app->get('get_product_by_section', '/api/section/{name}/{offset}', [Product::class, 'getBySection'], ['offset' => 0]);
$app->get('get_product_by_hierarchy', '/api/sections/{name}/{offset}', [Product::class, 'getBySections'], ['offset' => 0]);
$app->get('get_hierarchy', '/api/hierarchy/{pretty}', [Product::class, 'getHierarchy'], ['pretty' => false]);

$app->add(Zipofar\Middleware\DummyMiddleware::class);

$app->run();











