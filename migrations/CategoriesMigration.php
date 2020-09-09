<?php

/*********************** START BOOTSTRAP *******************/
declare(strict_types=1);
use App\Entity\Category;


use App\Application\Handlers\HttpErrorHandler;
use App\Application\Handlers\ShutdownHandler;
use App\Application\ResponseEmitter\ResponseEmitter;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

if (false) { // Should be set to true in production
	$containerBuilder->enableCompilation(__DIR__ . '/../var/cache');
}

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);


// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);


// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);


// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register middleware
$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

// Register routes
$routes = require __DIR__ . '/../app/routes.php';
$routes($app);
/*********************** END BOOTSTRAP *******************/

$categories = require __DIR__ . '/data/categories.php';

foreach ($categories() as $category_key => $category_value) {
  $category = new Category();
  $category->setMachineName($category_value['machine_name']);
  $category->setName($category_value['name']);

  $container->get('entity_manager')->persist($category);
  $container->get('entity_manager')->flush();
  echo "Created category with ID " . $category->getId() . "\n";
}
