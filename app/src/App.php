<?php
/**
 * Created by PhpStorm.
 * User: ingprog
 * Date: 11.09.18
 * Time: 12:54
 */

namespace Zipofar;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class App
{
    protected $container;

    public function __construct($container = [])
    {
        if (is_array($container)) {
            $builder = new \DI\ContainerBuilder();
            $builder->useAnnotations(false);
            $builder->addDefinitions($this->registerDefaultServices());
            $builder->addDefinitions(require '../src/settings.php');
            $container = $builder->build();
        }

        if (!$container instanceof ContainerInterface) {
            throw new \InvalidArgumentException('Expected Psr\Container\ContainerInterface');
        }
        $this->container = $container;
    }

    private function registerDefaultServices()

    {
        $dotenv = new \Dotenv\Dotenv(__DIR__.'/..');
        $dotenv->safeLoad();

        return [
            RouteCollection::class => function () {
                return new RouteCollection();
            },
            Request::class => function () {
                return Request::createFromGlobals();
            },
            Response::class => function () {
                return new Response('', 200);
            },
            UrlMatcher::class => function (ContainerInterface $container) {
                $context = new RequestContext();
                $context->fromRequest($container->get(Request::class));
                $routes = $container->get(RouteCollection::class);
                return new UrlMatcher($routes, $context);
            },
            Resolver::class => function ($container) {
                return new Resolver($container);
            },
            LoggerInterface::class => function ($container) {
                $settings = $container->get('settings')['logger'];
                $logger = new \Monolog\Logger($settings['name']);
                $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
                $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

                $handler = new \Monolog\ErrorHandler($logger);
                $handler->registerErrorHandler([], false);
                $handler->registerExceptionHandler();
                $handler->registerFatalHandler();

                return $logger;
            },
        ];
    }


    public function getContainer()
    {
        return $this->container;
    }

    public function get($pattern, $callable, $options = [])
    {
        return $this->map(['GET'], $pattern, $callable, $options);
    }

    public function post($pattern, $callable, $options = [])
    {
        return $this->map(['POST'], $pattern, $callable, $options);
    }

    public function map($methods, $pattern, $callable, $options = [])
    {
        if ($callable instanceof \Closure) {
            $newCallable = $callable->bindTo($this->container);
        } else {
            $newCallable = $callable;
        }

        $routes = $this->container->get(RouteCollection::class);
        $defaults = array_merge(['_controller' => $newCallable], $options);
        $route = new Route($pattern, $defaults);
        $route->setMethods($methods);
        $routes->add($pattern, $route);

        return $routes;
    }

    public function run()
    {
        $resolver = $this->container->get(Resolver::class);
        $matcher = $this->container->get(UrlMatcher::class);
        $request = $this->container->get(Request::class);

        try {
            $routeAttributes = $matcher->match($request->getPathInfo());
            $response = $resolver->resolve(
                $routeAttributes,
                $request,
                $this->container->get(Response::class)
            );
        } catch (ResourceNotFoundException $exception) {
            $response = new Response('Not Found This Route', 404);
        } catch (\Exception $e) {
            $response = new Response('Error', 500);
        }
        $response->send();
    }


}