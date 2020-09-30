<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => true, // Should be set to false in production
            'base_url' => $_ENV['BASE_URL'],
            'JWTauth' => [
                'secret' => $_ENV['JWT_SECRET'],
                'users' => [
                    $_ENV['USER'] => $_ENV['PASS'],
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
                        __DIR__ . '/../src/Entity'
                    ],
                    'auto_generate_proxies' => true,
                    'proxy_dir' =>  __DIR__ . '/../cache/proxies',
                    'cache' => null,
                ],
                'connection' => [
                    'driver'   => 'pdo_mysql',
                    'host'     => $_ENV['DB_HOST'],
                    'dbname'   => $_ENV['DB_NAME'],
                    'user'     => $_ENV['DB_USER'],
                    'password' => $_ENV['DB_PASS'],
                ],
            ],
        ],
    ]);
};
