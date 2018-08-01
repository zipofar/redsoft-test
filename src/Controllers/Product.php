<?php

namespace Zipofar;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Model\Product as MProduct;

class Product
{
    private $product;

    public function __construct()
    {
        $this->product = new MProduct();
    }

    private function buildResponse($response)
    {
        $newResponse = [
            'meta' => [
                'number_of_records' => count($response),
            ],
            'payload' => $response,
        ];

        if (empty($response)) {

            $newResponse['payload'] = '{}';

            return new Response(json_encode($newResponse), Response::HTTP_NOT_FOUND, ['content-type' => 'application/json']);
        }

        return new Response(json_encode($newResponse), Response::HTTP_OK, ['content-type' => 'application/json']);
    }

    public function getById(Request $request, $attributes)
    {
        $id = $attributes['id'];
        $res = $this->product->getById($id);

        return $this->buildResponse($res);
    }

    public function getBySubStrName(Request $request, $attributes)
    {
        $name = $attributes['name'];
        $res = $this->product->getBySubStrName($name);

        return $this->buildResponse($res);
    }

    public function getByBrand(Request $request, $attributes)
    {
        $name = $attributes['name'];
        $res = $this->product->getByBrand($name);

        return $this->buildResponse($res);
    }

    public function getBySection(Request $request, $attributes)
    {
        $name = $attributes['name'];
        $res = $this->product->getBySection($name);

        return $this->buildResponse($res);
    }

    public function getBySections(Request $request, $attributes)
    {
        $name = $attributes['name'];
        $res = $this->product->getBySections($name);

        return $this->buildResponse($res);
    }
}
