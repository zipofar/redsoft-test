<?php

namespace Zipofar\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zipofar\Misc\Helper;
use Zipofar\Model\MSection;

class Section
{
    protected $section;
    protected $response;

    public function __construct(Response $response, MSection $section)
    {
        $this->product = $section;
        $this->response = $response;
    }

    public function getById($attributes)
    {
        $id = $attributes['id'] ?? null;
        $res = $this->product->getById($id);
        $countRecords = empty($res) ? 0 : 1;
        return $this->buildResponse($res, $countRecords);
    }

    public function showSections($attributes, Request $request)
    {
        $params = $request->query->all();
        $hierarchy = $this->product->getHierarchy();

        if (!empty($hierarchy)) {
            $ast = Helper::buildTreeFromFlatNested($hierarchy);
        } else {
            $ast = [];
        }

        if (isset($params['pretty'])) {
            $list = Helper::buildListFromAst($ast);
            $this->response->setStatusCode(Response::HTTP_OK);
            $this->response->setContent($list);
            $this->response->headers->set('content-type', 'text/html');
            return $this->response;
        }

        return $this->buildResponse($ast, 1);
    }

    public function getBySubStrName($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySubStrName($name, $offset);

        return $this->buildResponse($res, sizeof($res));
    }

    public function getBySection($attributes)
    {
        $name = $attributes['name'];
        $offset = intval($attributes['offset']);

        $res = $this->product->getBySection($name, $offset);

        return $this->buildResponse($res, sizeof($res));
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

        return $this->buildResponse($res, sizeof($res));
    }

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
}
