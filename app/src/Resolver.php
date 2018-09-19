<?php


namespace Zipofar;

use Zipofar\Exception\InvalidCallbackException;
use Zipofar\Exception\MethodNotFoundException;
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
    public function resolve($routeAttributes)
    {
        $callback = $routeAttributes['_controller'] ?? [];
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
            if (!method_exists($createdObj, $method)) {
                throw new MethodNotFoundException('Not exist method - ${$method}', $class, $method);
            }

            $fn = function (Request $request, Response $response) use ($callbackWithObject, $tokens) {
                $result = call_user_func($callbackWithObject, $tokens, $request, $response);
                if (!$result instanceof Response) {
                    return $response;
                }
                return $result;
            };

            return $fn;
        }

        if (is_callable($callback)) {
            $fn = function (Request $request, Response $response) use ($callback, $tokens) {
                $result =  call_user_func($callback, $request, $response, $tokens);
                if (!$result instanceof Response) {
                    return $response;
                }
                return $result;
            };

            return $fn;
        }

        throw new InvalidCallbackException ('${$callback} is not a callable function');
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