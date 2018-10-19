<?php

namespace Zipofar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Misc\ResponseBuilder;
use Zipofar\Model\MProduct;

class Product
{
    use ResponseBuilder;

    /**
     * Model product
     *
     * @var MProduct
     */
    protected $product;

    /**
     * Object Response
     *
     * @var Response
     */
    protected $response;

    /**
     * Product constructor
     *
     * @param Response $response Response object
     * @param MProduct $product  Model of Product
     */
    public function __construct(Response $response, MProduct $product)
    {
        $this->product = $product;
        $this->response = $response;
    }

    public function getById($attributes)
    {
        $id = $attributes['id'] ?? null;
        $res = $this->product->getById($id);
        $countRecords = empty($res) ? 0 : 1;
        return $this->buildResponse($res, $countRecords);
    }

    public function showProducts($attributes, Request $request)
    {
        $params = $request->query->all();
        $res = $this->product->getProducts($params);
        return $this->buildResponse($res, sizeof($res));
    }

    public function showProductsInSection($attributes, Request $request)
    {
        $params = $request->query->all();
        $id = $attributes['id'];
        $res = $this->product->getProductsInSection($id, $params);

        return $this->buildResponse($res, sizeof($res));
    }

    public function showProductsInSectionSub($attributes, Request $request)
    {
        $params = $request->query->all();
        $id = $attributes['id'];
        $res = $this->product->showProductsInSectionSub($id, $params);

        return $this->buildResponse($res, sizeof($res));
    }


    public function addProduct($attributes, Request $request)
    {

        $product = json_decode($request->getContent(), true);
        $lastId = $this->product->addProduct($product);

        $this->response->setStatusCode(Response::HTTP_CREATED);
        $this->response->headers->set('Location', "/api/products/{$lastId}");

        return $this->response;
    }

    public function deleteProduct($attributes)
    {
        $id = $attributes['id'];
        $this->product->deleteProduct($id);

        return $this->response->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    public function putProduct($attributes, Request $request)
    {
        $id = $attributes['id'];
        $product = json_decode($request->getContent(), true);
        $product['id'] = $id;
        $this->product->putProduct($product);

        $this->response->setStatusCode(Response::HTTP_CREATED);
        $this->response->headers->set('Location', "/api/products/{$id}");

        return $this->response;
    }
}
