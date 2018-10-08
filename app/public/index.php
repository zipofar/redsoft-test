<?php

require_once "../vendor/autoload.php";

use Zipofar\Controller\Product;
use Zipofar\Controller\Section;

$app = new Zipofar\App();

$app->get('product.show', '/api/products/{id}', [Product::class, 'getById']);
$app->get('products.show', '/api/products', [Product::class, 'showProducts']);

$app->get('section.show', '/api/sections/{id}', [Section::class, 'getById']);
$app->get('sections.show', '/api/sections', [Section::class, 'showSections']);
$app->get('section_products.show', '/api/sections/{id}/products', [Product::class, 'showProductsInSection']);


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

?>

/api/products/{id}
/api/products
/api/products?brand=Disma&price=20&name=Hurma
/api/products?brand=Disma|Milk and Way|Magnit


/api/categories - return id, name (ast mode)
/api/categories/{id} - return id, name

/api/categories/{id}/products
/api/categories/{id}/products?brand=Disma&price=20&name=Hurma







