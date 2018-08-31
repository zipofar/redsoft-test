<?php

namespace Zipofar;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Core
{
    protected $routes;
    private $di_container;
    private $request;

    public function __construct()
    {
        $this->setupDotenv();
        $this->routes = new RouteCollection();
        $this->di_container = new \DI\Container();
        $this->request = Request::createFromGlobals();
        $_ENV['APP_DEBUG'] === 'true' ?: $this->setupLogger();
    }

    private function setupLogger()
    {
        $logger = new Logger('general');
        $logger->pushHandler(new StreamHandler(__DIR__.'/../log/app.log', Logger::WARNING));
        $handler = new \Monolog\ErrorHandler($logger);
        $handler->registerErrorHandler([], false);
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();
    }

    private function setupDotenv()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__."/../");
        $dotenv->load();
    }

    public function run()
    {
        $path = $this->request->getPathInfo();
        $context = new RequestContext();
        $context->fromRequest($this->request);

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

        $response->send();
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
