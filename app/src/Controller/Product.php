<?php

namespace Zipofar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Model\MProduct;
use Zipofar\Misc\Helper;

class Product
{
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

    /**
     * Response builder
     *
     * @param array $response       Payload data for response
     * @param integer $countRecords Count records from model
     * @return Response
     */
    protected function buildResponse($response, $countRecords)
    {
        $newResponse = [
            'meta' => [
                'number_of_records' => $countRecords,
            ],
            'payload' => $response,
        ];

        if (empty($response)) {
            $emptyPayload = new \stdClass();
            $newResponse['payload'] = $emptyPayload;
            $statusCode = Response::HTTP_NOT_FOUND;
        } else {
            $statusCode = Response::HTTP_OK;
        }

        $this->response->setStatusCode($statusCode);
        $this->response->setContent(json_encode($newResponse));
        $this->response->headers->set('content-type', 'application/json');

        return $this->response;
    }

    /**
     * Get plain hierarchy, build ast and return json or html list (<ul></ul>)
     *
     * @param array $attributes Attributes of Request
     *
     * @return Response
     */
    public function getHierarchy($attributes)
    {
        $pretty = $attributes['pretty'] === false ? false : true;
        $hierarchy = $this->product->getHierarchy();

        if (!empty($hierarchy)) {
            $ast = Helper::buildTreeFromFlatNested($hierarchy);
        }

        if ($pretty) {
            $list = Helper::buildListFromAst($ast);
            $this->response->setStatusCode(Response::HTTP_OK);
            $this->response->setContent($list);
            $this->response->headers->set('content-type', 'text/html');
            return $this->response;
        }

        return $this->buildResponse($ast, 1);
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

    public function addProduct($attributes, Request $request) :void
    {
        $product = $request->request->get('product');
        $this->product->addProduct($product);
    }

    public function deleteProduct($attributes, Request $request) :void
    {
        $productId = $request->request->get('product')['id'];
        echo "DELETE\r\n";
        print_r($attributes);
        print_r($request->request->all());
        //$this->product->deleteProduct($productId);
    }

    public function putProduct($attributes, Request $request) :void
    {
        //$product = $request->request->get('product');
        echo "PUT\r\n";
        print_r($attributes);
        print_r($request->request->all());
        //$this->product->putProduct($product);
    }

}
