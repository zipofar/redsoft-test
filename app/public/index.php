<?php

require_once "../vendor/autoload.php";

use Zipofar\Controller\Product;
use Zipofar\Controller\Section;

$dotenv = new \Dotenv\Dotenv(__DIR__.'/..');
$dotenv->safeLoad();

$app = new Zipofar\App();

$app->get('product.show', '/api/products/{id}', [Product::class, 'getById']);
$app->get('products.show', '/api/products', [Product::class, 'showProducts']);
$app->get('productsInSection.show', '/api/sections/{id}/products', [Product::class, 'showProductsInSection']);
$app->get('productsInSection.show', '/api/sections/{id}/sub/products', [Product::class, 'showProductsInSectionSub']);
$app->post('product.store', '/api/products', [Product::class, 'addProduct']);
$app->delete('product.destroy', '/api/products/{id}', [Product::class, 'deleteProduct']);
$app->put('product.update', '/api/products/{id}', [Product::class, 'putProduct']);

$app->get('section.show', '/api/sections/{id}', [Section::class, 'getById']);
$app->get('sections.show', '/api/sections', [Section::class, 'showSections']);


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
/api/categories/{id}/children/products?brand=Disma&price=20&name=Hurma








