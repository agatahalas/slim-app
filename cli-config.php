<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use DI\ContainerBuilder;

require 'vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Load envs
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

// Set up settings
$settings = require __DIR__ . '/app/settings.php';
$settings($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

$settings = $container->get('settings')['doctrine'];

$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
    $settings['meta']['entity_path'],
    $settings['meta']['auto_generate_proxies'],
    $settings['meta']['proxy_dir'],
    $settings['meta']['cache'],
    false
);

$em = \Doctrine\ORM\EntityManager::create($settings['connection'], $config);

return ConsoleRunner::createHelperSet($em);
