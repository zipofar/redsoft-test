<?php

namespace Zipofar;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefferedCallable
{
    private $callable;
    private $container;

    public function __construct(ContainerInterface $container, $callable)
    {
        $this->callable = $callable;
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        $createdObj = $this->container->get($this->callable);
        $response = $createdObj($request, $response, $next);

        return $response;
    }
}
