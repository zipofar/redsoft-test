<?php

use PHPUnit\Framework\TestCase;
use Zipofar\Controller\Product;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Model\MProduct;

class ProductTest extends TestCase
{
    public function testNotEmptyGetById()
    {
        $returnedFromModel = [
            'id' => '1',
            'name' => 'FoodVegRedSour',
            'availability' => '1',
            'price' => '1.99',
            'brand' => 'Russia Kolhoz'
        ];

        $expectedContent = '{"meta":{"number_of_records":1},"payload":{"id":"1","name":"FoodVegRedSour","availability":"1","price":"1.99","brand":"Russia Kolhoz"}}';

        $stubProduct = $this->createMock(MProduct::class);
        $stubProduct->method('getById')->willReturn($returnedFromModel);

        $product = new Product(new Response(), $stubProduct);

        $res = $product->getById(['id' => 1]);

        $this->assertJsonStringEqualsJsonString($expectedContent, $res->getContent());
        $this->assertEquals('200', $res->getStatusCode());
    }

    public function testEmptyGetById()
    {
        $expectedContent = '{"meta":{"number_of_records":0},"payload":{}}';

        $stubProduct = $this->createMock(MProduct::class);
        $stubProduct->method('getById')->willReturn([]);

        $product = new Product(new Response(), $stubProduct);

        $res = $product->getById(['id' => 0]);

        $this->assertJsonStringEqualsJsonString($expectedContent, $res->getContent());
        $this->assertEquals('404', $res->getStatusCode());
    }

    public function testGetBySubStrName()
    {
        $this->getBySomeMethod('getBySubStrName', 'getBySubStrName');
    }

    public function testGetByBrand()
    {
        $this->getBySomeMethod('getByBrand', 'getByBrand');
    }

    public function testGetBySection()
    {
        $this->getBySomeMethod('getBySection', 'getBySection');
    }

    public function testGetBySections()
    {
        $this->getBySomeMethod('getBySections', 'getBySections');
    }

    private function getBySomeMethod($stubMethod, $callableMethod)
    {
        $returnedFromModel = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $expectedContent = [
            'meta' => [
                'number_of_records' => sizeof($returnedFromModel),
            ],
            'payload' => $returnedFromModel,
        ];

        $stubMProduct = $this->createMock(MProduct::class);
        $stubMProduct->method($stubMethod)->willReturn($returnedFromModel);

        $product = new Product(new Response(), $stubMProduct);

        $params = ['name' => 'someProductOrOtherName', 'offset' => '0'];
/*
        $stubMProduct->expects($this->once())
            ->method($stubMethod)
            ->with(
                $this->equalTo($expName),
                $this->equalTo(1)
            );
*/
        $res = call_user_func([$product, $callableMethod], $params);

        $this->assertJsonStringEqualsJsonString(json_encode($expectedContent), $res->getContent());
        $this->assertEquals('200', $res->getStatusCode());
    }
}