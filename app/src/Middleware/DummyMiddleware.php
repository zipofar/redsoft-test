<?php
/**
 * Created by PhpStorm.
 * User: ingprog
 * Date: 17.09.18
 * Time: 16:30
 */

namespace Zipofar\Middleware;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DummyMiddleware
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $start = microtime();
        $response = $next($request, $response);
        $stop = microtime();
        $response->headers->set('X-Time', $stop-$start);
        return $response;
    }
}