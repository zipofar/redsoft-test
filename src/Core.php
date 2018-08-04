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

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function handle(Request $request)
    {
        $path = $request->getPathInfo();

        $context = new RequestContext();
        $context->fromRequest($request);

        try {
            $matcher = new UrlMatcher($this->routes, $context);
            $attributes = $matcher->match($path);

            switch ($attributes['_controller']['class']) {
                case 'Product':
                    $product = new Model\MProduct(['limit' => 10]);
                    $response = new Response();
                    $controller = new Controller\Product ($response, $product);
                    break;
            }

            $response = call_user_func([$controller, $attributes['_controller']['method']], $request, $attributes);
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
            ]
        ));
    }
}
