<?php

namespace Zipofar;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Core
{
    protected $routes;
    private $di_container;

    public function __construct($di_container)
    {
        $this->routes = new RouteCollection();
        $this->di_container = $di_container;
    }

    public function handle(Request $request)
    {
        $path = $request->getPathInfo();
        $context = new RequestContext();
        $context->fromRequest($request);

        try {
            $matcher = new UrlMatcher($this->routes, $context);
            $attributes = $matcher->match($path);
            $calledClass = $attributes['_controller']['class'];

            switch ($calledClass) {
                case 'Product':
                    $controller = $this->di_container->get('\Zipofar\Controller\Product');
                    break;
            }

            $response = call_user_func([$controller, $attributes['_controller']['method']], $attributes);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('{"meta":{"error":"wrong uri"}}', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    public function addRoute($route, $controller, $method)
    {
        $this->routes->add($route, new Route(
            $route,
            [
                '_controller' => ['class' => $controller, 'method' => $method],
                'offset' => 0,
                'pretty' => false,
            ]
        ));
    }
}
