<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'JWTauth' => [
                'secret' => 'FBD2F4C3DD7AE9C94B6B408A62513B58CD4A2AE18002D5CA2D1068F9',
                'users' => [
                    'admin' => 'zsZ@aVn6Tq#a\e(',
                ]
            ],
            'logger' => [
                'name' => 'slim-app',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'doctrine' => [
                'meta' => [
                    'entity_path' => [
                        '../app/src/Entity'
                    ],
                    'auto_generate_proxies' => true,
                    'proxy_dir' =>  __DIR__ . '/../cache/proxies',
                    'cache' => null,
                ],
                'connection' => [
                    'driver'   => 'pdo_mysql',
                    'host'     => 'database',
                    'dbname'   => 'lamp',
                    'user'     => 'lamp',
                    'password' => 'lamp',
                ],
            ],
        ],
    ]);
};
