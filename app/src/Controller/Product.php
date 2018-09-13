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
     * @param array $response Payload data for response
     *
     * @return Response
     */
    protected function buildResponse($response)
    {
        $newResponse = [
            'meta' => [
                'number_of_records' => count($response),
            ],
            'payload' => $response,
        ];

        if (empty($response)) {
            $emptyPayload = new \stdClass();
            $newResponse['payload'] = $emptyPayload;
            $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
        } else {
            $this->response->setStatusCode(Response::HTTP_OK);
        }

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
            $ast = Helper::buildTree($hierarchy);
        }

        if ($pretty) {
            $list = Helper::buildListFromAst($ast);
            $this->response->setStatusCode(Response::HTTP_OK);
            $this->response->setContent($list);
            $this->response->headers->set('content-type', 'text/html');
            return $this->response;
        }

        return $this->buildResponse($ast);
    }

    /**
     * Get product by ID
     *
     * @param array $attributes Attributes of Request
     *
     * @return Response
     */
    public function getById($attributes)
    {
        $id = $attributes['id'] ?? null;
        $res = $this->product->getById($id);

        return $this->buildResponse($res);
    }

    /**
     * Get product by start part product name
     *
     * @param array $attributes Attributes of Request
     *
     * @return Response
     */
    public function getBySubStrName($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySubStrName($name, $offset);

        return $this->buildResponse($res);
    }

    /**
     * Get product by brand name
     *
     * @param array $attributes Attributes of Request
     *
     * @return Response
     */
    public function getByBrand($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getByBrand($name, $offset);

        return $this->buildResponse($res);
    }

    /**
     * Get product by specific section of product
     *
     * @param array $attributes Attributes of Request
     *
     * @return Response
     */
    public function getBySection($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySection($name, $offset);

        return $this->buildResponse($res);
    }

    /**
     * Get product by path tree sections. Like a Electrinics->TV->LCD...
     *
     * @param array $attributes Attributes of Request
     *
     * @return Response
     */
    public function getBySections($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySections($name, $offset);

        return $this->buildResponse($res);
    }
}
