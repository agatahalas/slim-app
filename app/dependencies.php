<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ],
    [
        'entity_manager' => function (ContainerInterface $c) {
            $settings = $c->get('settings');
            $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
                $settings['doctrine']['meta']['entity_path'],
                $settings['doctrine']['meta']['auto_generate_proxies'],
                $settings['doctrine']['meta']['proxy_dir'],
                $settings['doctrine']['meta']['cache'],
                false
            );
            return \Doctrine\ORM\EntityManager::create($settings['doctrine']['connection'], $config);
        },
        'validator' => function (ContainerInterface $c) {
            return Respect\Validation\Validator::create();
        },
    ],
    [
        'icon' => function (ContainerInterface $c) {
            return new App\Entity\Icon();
        },
        'category' => function (ContainerInterface $c) {
          return new App\Entity\Category();
        }
    ],
    [
        'App\Application\Actions\Icon\IconAction' => function ($c) {
            return new App\Application\Actions\Icon\IconAction($c->get('entity_manager'), $c->get('icon'), $c->get('validator'));
        },
        'App\Application\Actions\Category\CategoryAction' => function ($c) {
          return new App\Application\Actions\Category\CategoryAction($c->get('entity_manager'), $c->get('category'), $c->get('validator'));
        }
    ]);
};
