<?php

namespace Zipofar\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Zipofar\Misc\Helper;
use Zipofar\Misc\ResponseBuilder;
use Zipofar\Model\MSection;

class Section
{
    use ResponseBuilder;

    protected $section;
    protected $response;

    public function __construct(Response $response, MSection $section)
    {
        $this->section = $section;
        $this->response = $response;
    }

    public function getById($attributes)
    {
        $id = $attributes['id'] ?? null;
        $res = $this->section->getById($id);
        $countRecords = empty($res) ? 0 : 1;
        return $this->buildResponse($res, $countRecords);
    }

    public function showSections($attributes, Request $request)
    {
        $params = $request->query->all();
        $hierarchy = $this->section->getHierarchy();

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

    public function addSection($attributes, Request $request)
    {
        $params = json_decode($request->getContent(), true);
        $lastId = $this->section->addSection($params);

        $this->response->setStatusCode(Response::HTTP_CREATED);
        $this->response->headers->set('Location', "/api/sections/{$lastId}");

        return $this->response;
    }

    public function deleteSection($attributes)
    {
        $id = $attributes['id'];
        $this->section->deleteSection($id);

        return $this->response->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    public function putSection($attributes, Request $request)
    {
        $id = $attributes['id'];
        $section = json_decode($request->getContent(), true);
        $section['id'] = $id;
        $this->section->updateSection($section);

        $this->response->setStatusCode(Response::HTTP_CREATED);
        $this->response->headers->set('Location', "/api/sections/{$id}");

        return $this->response;
    }
}
