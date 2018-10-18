<?php

namespace Zipofar;

use Symfony\Component\Routing\RequestContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Psr\Container\ContainerInterface;
use Zipofar\Database\ZPdo;
use Zipofar\Misc\Helper;

class DefaultServiceProvider
{
    public static function register()
    {
        return [
            RouteCollection::class => function () {
                return new RouteCollection();
            },
            Request::class => function () {
                Request::enableHttpMethodParameterOverride();
                $request = Request::createFromGlobals();
                return $request;
            },
            Response::class => function () {
                return new Response();
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
            ZPdo::class => function ($container) {
                return new ZPdo($container);
            },
            Handler\Error::class => function () { return new Handler\Error(); },
            Handler\PhpError::class => function () { return new Handler\PhpError(); },
        ];
    }
}