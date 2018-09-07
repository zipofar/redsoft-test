<?php

namespace Zipofar;

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use DI\Container;


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
     * @var Logger
     */
    protected $logger;

    /**
     * Core constructor.
     */
    public function __construct(Container $container)
    {
        $this->di_container = $container;
        $this->options = $options = $this->setupOptions();
        $this->setupDotenv($options['pathDotEnvDir']);
        $this->routes = new RouteCollection();
        $this->request = Request::createFromGlobals();
        $this->logger = $this->setupLogger($options['pathLogDir'], $_ENV['APP_DEBUG']);
    }

    /**
     *  Setup options
     *
     * @return void
     */
    protected function setupOptions()
    {
        $appPath = dirname(__DIR__);
        $options = [
            'pathLogDir' => $appPath.'/storage/logs',
            'pathDotEnvDir' => $appPath,
        ];
        return $options;
    }

    /**
     * Setup Logger. If app in production environment, set APP_DEBUG = false
     * and move all logs to file
     * else use default setting from php.ini and show errors to stdout
     *
     * @param string $pathLogDir Path to log file
     * @param string $status      Status debug mode TRUE or FALSE
     *
     * @throws \Exception
     *
     * @return Logger
     */
    protected function setupLogger($pathLogDir, $status = 'false')
    {
        $logger = new Logger('RedSoft@general');

        if (strtolower($status) === 'false') {
            $logger->pushHandler(new RotatingFileHandler($pathLogDir.'/dailyLog.log', 0, Logger::DEBUG));
            $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
        } else {
            $logger->pushHandler(new StreamHandler($pathLogDir.'/app.log', Logger::DEBUG));
            $logger->pushHandler(new ChromePHPHandler());
        }

        $handler = new \Monolog\ErrorHandler($logger);
        $handler->registerErrorHandler([], false);
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();

        return $logger;
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
        $this->logger->info('RUN APP');

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
