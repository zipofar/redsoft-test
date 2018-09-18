<?php

namespace Zipofar;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait MiddlewareAwareStack
{
    protected $tip;

    protected function addMiddleware(callable $callable)
    {
        if (is_null($this->tip)) {
            $this->seedMiddlewareStack();
        }
        $next = $this->tip;
        $this->tip = function (Request $request, Response $response) use ($callable, $next) {
            $result = call_user_func($callable, $request, $response, $next);

            if (!$result instanceof Response) {
                throw new \UnexpectedValueException(
                    'Middleware must return instance of Symfony\Component\HttpFoundation\Response'
                );
            }

            return $result;
        };

        return $this;
    }

    protected function seedMiddlewareStack(callable $kernel = null)
    {
        if (!is_null($this->tip)) {
            throw new \RuntimeException('MiddlewareStack can only be seeded once.');
        }
        if ($kernel === null) {
            $kernel = $this;
        }
        $this->tip = $kernel;
    }

    public function callMiddlewareStack(Request $request, Response $response)
    {
        if (is_null($this->tip)) {
            $this->seedMiddlewareStack();
        }
        /** @var callable $start */
        $start = $this->tip;
        $response = $start($request, $response);
        return $response;
    }
}
