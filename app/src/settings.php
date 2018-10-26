<?php

return [
    'settings' => [

        // Monolog settings
        'logger' => [
            'name' => 'redsoft-app',
            'path' => isset($_ENV['DOCKER']) ? 'php://stdout' : __DIR__ . '/../storage/logs/app.log',
            'level' => \Monolog\Logger::INFO,
            'slack_webhook' => $_ENV['SLACK_WEBHOOK'],
            'slack_channel' => $_ENV['SLACK_CHANNEL'],
        ],
    ]
];
