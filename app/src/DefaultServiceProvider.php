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

class DefaultServiceProvider
{
    public static function register()
    {
        return [
            RouteCollection::class => function () {
                return new RouteCollection();
            },
            Request::class => function () {
                return Request::createFromGlobals();
            },
            Response::class => function () {
                return new Response('', 200);
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
        ];
    }
}