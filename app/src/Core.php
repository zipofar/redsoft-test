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
    /**
     * Route collection
     *
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Object DI container
     *
     * @var \DI\Container
     */
    protected $di_container;

    /**
     * Request object
     *
     * @var Request object
     */
    protected $request;

    /**
     * Array options
     *
     * @var array
     */
    protected $options;

    /**
     * Core constructor.
     */
    public function __construct()
    {
        $this->setupOptions();
        $this->setupDotenv($this->options['pathDotEnvDir']);
        $this->routes = new RouteCollection();
        $this->di_container = new \DI\Container();
        $this->request = Request::createFromGlobals();

        $logFilePath = $this->options['pathLogDir'].DIRECTORY_SEPARATOR.$this->options['fileNameLog'];
        $this->setupLogger($logFilePath);
    }

    /**
     *  Setup options
     *
     * @return void
     */
    protected function setupOptions()
    {
        $appPath = dirname(__DIR__);

        $this->options = [
            'pathLogDir' => $appPath.DIRECTORY_SEPARATOR.'log',
            'fileNameLog' => 'app.log',
            'pathDotEnvDir' => $appPath,
        ];
    }

    /**
     * Setup Logger if app in production environment
     * else use default setting from php.ini
     *
     * @param string $logFilePath Path to log file
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function setupLogger($logFilePath)
    {
        if ($_ENV['APP_DEBUG'] === 'true') {
            $logger = new Logger('general');
            $logger->pushHandler(new StreamHandler($logFilePath, Logger::WARNING));
            $handler = new \Monolog\ErrorHandler($logger);
            $handler->registerErrorHandler([], false);
            $handler->registerExceptionHandler();
            $handler->registerFatalHandler();
        }
    }

    /**
     * Load Dotenv.
     *
     * @param string $pathEnvDir Path to .env file
     *
     * @return void
     */
    protected function setupDotenv($pathEnvDir)
    {
        $dotenv = new \Dotenv\Dotenv($pathEnvDir);
        $dotenv->load();
    }

    /**
     * Begin processing of route
     *
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     *
     * @return void
     */
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

    /**
     *  ADD ROUTE
     *
     * @param string $route      Route like a '/api/id/{id}'
     * @param string $controller Controller name
     * @param string $method     Method name
     *
     * @return void
     */
    public function addRoute($route, $controller, $method)
    {
        $options = [
            '_controller' => ['class' => $controller, 'method' => $method],
            'offset' => 0,
            'pretty' => false,
        ];

        $this->routes->add($route, new Route($route, $options));
    }
}
