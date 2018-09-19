<?php

namespace Zipofar;

use MongoDB\Driver\Exception\UnexpectedValueException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class App
{
    use MiddlewareAwareStack;

    protected $container;

    public function __construct($container = [])
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__.'/..');
        $dotenv->safeLoad();

        if (is_array($container)) {
            $builder = new \DI\ContainerBuilder();
            $builder->useAnnotations(false);
            $builder->addDefinitions(DefaultServiceProvider::register());
            $builder->addDefinitions(require '../src/settings.php');
            $container = $builder->build();
        }

        if (!$container instanceof ContainerInterface) {
            throw new \InvalidArgumentException('Expected Psr\Container\ContainerInterface');
        }
        $this->container = $container;
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

    public function add($callback)
    {
        if (is_string($callback)) {
            $callback = new DefferedCallable($this->container, $callback);
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Unexpceted callback middleware ${$callback}');
        }

        $this->addMiddleware($callback);
    }

    public function run()
    {
        $request = $this->container->get(Request::class);
        $response = new Response();

        try {
            ob_start();
            $response = $this->callMiddlewareStack($request, $response);
        } catch (\Exception $e) {
            $response = new Response($e->getMessage());
            //$response = $this->handleException($e, $request, $response);
        } catch (\Throwable $e) {
            $response = new Response($e->getMessage());
            //$response = $this->handlePhpError($e, $request, $response);
        } finally {
            $output = ob_get_clean();
        }
        $response->setContent($response->getContent().$output);
        $response->send();
    }

    public function __invoke(Request $request, Response $response)
    {
        $matcher = $this->container->get(UrlMatcher::class);
        $resolver = $this->container->get(Resolver::class);
        $routeAttributes = $matcher->match($request->getPathInfo());
        $callback = $resolver->resolve($routeAttributes);
        $response = $callback($request, $response);
        return $response;
    }
/*
    protected function handleException(\Exception $e, Request $request, Response $response)
    {
        if ($e instanceof MethodNotAllowedException) {
            $handler = 'notAllowedHandler';
            $params = [$e->getRequest(), $e->getResponse(), $e->getAllowedMethods()];
        } elseif ($e instanceof NotFoundException) {
            $handler = 'notFoundHandler';
            $params = [$e->getRequest(), $e->getResponse(), $e];
        } elseif ($e instanceof SlimException) {
            // This is a Stop exception and contains the response
            return $e->getResponse();
        } else {
            // Other exception, use $request and $response params
            $handler = 'errorHandler';
            $params = [$request, $response, $e];
        }

        if ($this->container->has($handler)) {
            $callable = $this->container->get($handler);
            // Call the registered handler
            return call_user_func_array($callable, $params);
        }

        // No handlers found, so just throw the exception
        throw $e;
    }*/
}