<?php

require_once "../vendor/autoload.php";

use Symfony\Component\HttpFoundation\Response;
use Zipofar\Controller\Product;
use Zipofar\Controller\Section;

$app = new Zipofar\App();

$app->get('index.show', '/', function ($request, Response $response) {
    return $response->setContent('{"meta":[],"payload":{"index":"This is a redsoft test project"}}');
});
$app->get('product.show', '/api/products/{id}', [Product::class, 'getById']);
$app->get('products.show', '/api/products', [Product::class, 'showProducts']);
$app->get('productsInSection.show', '/api/sections/{id}/products', [Product::class, 'showProductsInSection']);
$app->get('productsInSubSection.show', '/api/sections/{id}/sub/products', [Product::class, 'showProductsInSectionSub']);
$app->post('product.store', '/api/products', [Product::class, 'addProduct']);
$app->delete('product.destroy', '/api/products/{id}', [Product::class, 'deleteProduct']);
$app->put('product.update', '/api/products/{id}', [Product::class, 'putProduct']);

$app->get('section.show', '/api/sections/{id}', [Section::class, 'getById']);
$app->get('sections.show', '/api/sections', [Section::class, 'showSections']);

$app->post('sections.store', '/api/sections', [Section::class, 'addSection']);
$app->delete('sections.destroy', '/api/sections/{id}', [Section::class, 'deleteSection']);
$app->put('sections.update', '/api/sections/{id}', [Section::class, 'putSection']);

$app->add(Zipofar\Middleware\DummyMiddleware::class);

$app->run();

?>

/api/products/{id}
/api/products
/api/products?brand=Disma&price=20&name=Hurma
/api/products?brand=Disma|Milk and Way|Magnit


/api/sections - return id, name (ast mode)
/api/sections/{id} - return id, name

/api/sections/{id}/products
/api/sections/{id}/products?brand=Disma&price=20&name=Hurma
/api/sections/{id}/sub/products?brand=Disma&price=20&name=Hurma








