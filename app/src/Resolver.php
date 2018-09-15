<?php


namespace Zipofar;


use Dotenv\Exception\InvalidCallbackException;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Resolver
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /*
     * If $routeAttributes['_controller'] contain array, create object from class name
     * and call method that object
     * If $routeAttributes['_controller'] contain callback, call it
     */
    public function resolve(array $routeAttributes, Request $request, Response $response)
    {
        $callback = $routeAttributes['_controller'] ?? null;
        $tokens = $this->getTokens($routeAttributes);

        /*
         * callback example [\Zipofar\Controller\Hello::class, 'index']
         * callback example [\Zipofar\Controller\Hello::class]
         */
        if (is_array($callback) && sizeof($callback) > 0) {
            $class = $callback[0];
            $createdObj = $this->container->get($class);
            $callbackWithObject[] = $createdObj;
            $callbackWithObject[] = $method = $callback[1] ?? '__invoke';

            return call_user_func($callbackWithObject, $tokens);
        }

        if (is_callable($callback)) {
            return call_user_func($callback, $request, $response, $tokens);
        }

        throw new InvalidCallbackException();
    }

    /*
     * Return all enties where keys not begin with lodash
     */
    protected function getTokens($params = [])
    {
        return array_filter($params, function ($key) {
            return !stristr($key, '_');
        }, ARRAY_FILTER_USE_KEY);
    }
}