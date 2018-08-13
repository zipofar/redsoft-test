<?php

use PHPUnit\Framework\TestCase;
use Zipofar\Controller\Product;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Model\MProduct;

class ProductTest extends TestCase
{
    public function testNotEmptyGetById()
    {
        $expectedContent = '{"meta":{"number_of_records":1},"payload":{"id":"1"}}';

        $stubProduct = $this->createMock(MProduct::class);
        $stubProduct->method('getById')->willReturn(['id' => '1']);

        $response = new Response();
        $product = new Product($response, $stubProduct);

        $attributes['id'] = '1';
        $res = $product->getById($attributes);

        $this->assertEquals($expectedContent, $res->getContent());
        $this->assertEquals('200', $res->getStatusCode());
    }

    public function testEmptyGetById()
    {
        $expectedContent = '{"meta":{"number_of_records":0},"payload":{}}';

        $stubProduct = $this->createMock(MProduct::class);
        $stubProduct->method('getById')->willReturn([]);

        $response = new Response();
        $product = new Product($response, $stubProduct);

        $attributes['id'] = '1';

        $res = $product->getById($attributes);

        $this->assertEquals($expectedContent, $res->getContent());
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
        $expName = 'somename';
        $expectedContent = '{"meta":{"number_of_records":1},"payload":{"name":"'.$expName.'"}}';

        $stubProduct = $this->createMock(MProduct::class);
        $stubProduct->method($stubMethod)->willReturn(['name' => $expName]);

        $response = new Response();
        $product = new Product($response, $stubProduct);

        $attributes['name'] = $expName;
        $attributes['offset'] = '1';

        $stubProduct->expects($this->once())
            ->method($stubMethod)
            ->with(
                $this->equalTo($expName),
                $this->equalTo(1)
            );

        $res = call_user_func([$product, $callableMethod], $attributes);

        $this->assertEquals($expectedContent, $res->getContent());
        $this->assertEquals('200', $res->getStatusCode());
    }
}