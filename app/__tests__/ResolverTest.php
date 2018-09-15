<?php

use PHPUnit\Framework\TestCase;
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
        $this->request = new \Symfony\Component\HttpFoundation\Request();
        $this->response = new \Symfony\Component\HttpFoundation\Response();
    }

    public function test_ClojureCallbackWithoutTokens()
    {
        $expected = 'someResponse';
        $mockCallaback = function () use ($expected) { return $expected; };
        $routeAttributes = ['_controller' => $mockCallaback];

        $this->assertEquals(
            $expected,
            $this->resolver->resolve($routeAttributes, $this->request, $this->response)
        );

    }

    public function test_ClojureCallbackWithTokens()
    {
        $expected = ['id' => 1];
        $mockCallaback = function ($request, $response, $tokens) use ($expected) { return $tokens; };
        $routeAttributes = array_merge(['_controller' => $mockCallaback], $expected);

        $this->assertEquals(
            $expected,
            $this->resolver->resolve($routeAttributes, $this->request, $this->response)
        );

    }

    public function test_ClassWithInvokeMethodWithTokens()
    {
        $expected = ['id' => 1];
        $mockClass = [\Tests\DummyController::class];
        $routeAttributes = array_merge(['_controller' => $mockClass], $expected);

        $this->assertEquals(
            $expected,
            $this->resolver->resolve($routeAttributes, $this->request, $this->response)
        );

    }

    public function test_ClassWithIndexMethodWithTokens()
    {
        $expected = ['id' => 1];
        $mockClass = [\Tests\DummyController::class, 'index'];
        $routeAttributes = array_merge(['_controller' => $mockClass], $expected);

        $this->assertEquals(
            $expected,
            $this->resolver->resolve($routeAttributes, $this->request, $this->response)
        );

    }
}