<?php

namespace Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DummyController
{
    public function __invoke($tokens, Request $request, Response $response)
    {
        return $response->setContent("Response-{$tokens['id']}");
    }

    public function index($tokens, Request $request, Response $response)
    {
        return $response->setContent("Response-{$tokens['id']}");
    }
}