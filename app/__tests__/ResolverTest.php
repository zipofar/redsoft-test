<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zipofar\Resolver;
use Tests\DummyContainer;

class ResolverTest extends TestCase
{
    private $resolver;
    private $request;
    private $response;

    public function setUp()
    {
        $this->resolver = new Resolver(new DummyContainer());
        $this->request = new Request();
        $this->response = new Response();
    }

    public function test_ClojureCallbackWithoutTokens()
    {
        $mockCallaback = function ($request, $response) { return $response; };
        $routeAttributes = ['_controller' => $mockCallaback];

        $fn = $this->resolver->resolve($routeAttributes);
        $this->assertInstanceOf(Response::class, $fn($this->request, $this->response));

    }

    public function test_ClojureCallbackWithTokens()
    {
        $token = ['id' => 1];
        $expected = "Response-1";

        $mockCallaback = function ($request, $response, $tokens) {
            return $response->setContent("Response-{$tokens['id']}");
        };
        $routeAttributes = array_merge(['_controller' => $mockCallaback], $token);

        $fn = $this->resolver->resolve($routeAttributes);
        $this->assertEquals($expected, $fn($this->request, $this->response)->getContent());
    }

    public function test_ClassWithInvokeMethodWithTokens()
    {
        $token = ['id' => 1];
        $expected = "Response-1";

        $mockClass = [\Tests\DummyController::class];
        $routeAttributes = array_merge(['_controller' => $mockClass], $token);

        $fn = $this->resolver->resolve($routeAttributes);
        $this->assertEquals($expected, $fn($this->request, $this->response)->getContent());

    }

    public function test_ClassWithIndexMethodWithTokens()
    {
        $token = ['id' => 1];
        $expected = "Response-1";
        $mockClass = [\Tests\DummyController::class, 'index'];
        $routeAttributes = array_merge(['_controller' => $mockClass], $token);

        $fn = $this->resolver->resolve($routeAttributes);
        $this->assertEquals($expected, $fn($this->request, $this->response)->getContent());

    }
}