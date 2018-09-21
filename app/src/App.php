<?php

namespace Zipofar;

use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Zipofar\Handler\Error;
use Zipofar\Handler\PhpError;

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

    public function get($name, $pattern, $callable, $options = [])
    {
        return $this->map(['GET'], $name, $pattern, $callable, $options);
    }

    public function post($name, $pattern, $callable, $options = [])
    {
        return $this->map(['POST'], $name, $pattern, $callable, $options);
    }

    public function delete($name, $pattern, $callable, $options = [])
    {
        return $this->map(['DELETE'], $name, $pattern, $callable, $options);
    }

    public function put($name, $pattern, $callable, $options = [])
    {
        return $this->map(['PUT'], $name, $pattern, $callable, $options);
    }

    public function patch($name, $pattern, $callable, $options = [])
    {
        return $this->map(['PATCH'], $name, $pattern, $callable, $options);
    }

    public function map($methods, $name, $pattern, $callable, $options = [])
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
        $routes->add($name, $route);

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
        $response = $this->container->get(Response::class);

        try {
            ob_start();
            $response = $this->callMiddlewareStack($request, $response);
        } catch (\Exception $e) {
            $response = $this->handleException($e, $request, $response);
        } catch (\Throwable $e) {
            $response = $this->handlePhpError($e, $request, $response);
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

    protected function handleException(\Exception $e, Request $request, Response $response)
    {
        $callable = $this->container->get(Error::class);
        $params = [$request, $response, $e];
        return call_user_func_array($callable, $params);
    }

    protected function handlePhpError(\Throwable $e, Request $request, Response $response)
    {
        $callable = $this->container->get(PhpError::class);
        $params = [$request, $response, $e];
        return call_user_func_array($callable, $params);
    }
}