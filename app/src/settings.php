<?php

return [
    'settings' => [

        // Monolog settings
        'logger' => [
            'name' => 'redsoft-app',
            'path' => isset($_ENV['DOCKER']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ]
];
