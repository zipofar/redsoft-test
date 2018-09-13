<?php


namespace Zipofar;


use Dotenv\Exception\InvalidCallbackException;
use Prophecy\Exception\Doubler\ClassNotFoundException;
use Psr\Container\ContainerInterface;

class Resolver
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /*
     * If $routeAttributes['_controller'] contain array, create object from class name
     * And call method that object
     * If $routeAttributes['_controller'] contain callback, call it
     */
    public function resolve($routeAttributes = [], $request, $response)
    {
        $callback = $routeAttributes['_controller'] ?? [];
        $tokens = $this->getTokens($routeAttributes);

        /*
         * Like [\Zipofar\Controller\Hello::class, 'index']
         * or [\Zipofar\Controller\Hello::class]
         */
        if (is_array($callback) && sizeof($callback) > 0) {
            $class = $callback[0];
            $obj = $this->container->get($class);
            $callbackWithObject = [$obj];
            if (isset($callback[1])) {
                $method = $callback[1];
                $callbackWithObject[] = $method;
            }
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