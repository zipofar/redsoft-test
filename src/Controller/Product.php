<?php

namespace Zipofar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Model\MProduct;

class Product
{
    private $product;
    private $response;

    public function __construct(Response $response, MProduct $product)
    {
        $this->product = $product;
        $this->response = $response;
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
            $newResponse['payload'] = new \stdClass();
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
        } else {
            $this->response->setStatusCode(Response::HTTP_OK);
        }

        $this->response->setContent(json_encode($newResponse));
        $this->response->headers->set('content-type', 'application/json');

        return $this->response;
    }

    public function getById($attributes)
    {
        $id = $attributes['id'];
        $res = $this->product->getById($id);

        return $this->buildResponse($res);
    }

    public function getBySubStrName($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySubStrName($name, $offset);

        return $this->buildResponse($res);
    }

    public function getByBrand($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getByBrand($name, $offset);

        return $this->buildResponse($res);
    }

    public function getBySection($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySection($name, $offset);

        return $this->buildResponse($res);
    }

    public function getBySections($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySections($name, $offset);

        return $this->buildResponse($res);
    }
}
