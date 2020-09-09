<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/api/icons', function (Group $group) {
      $group->get('', 'App\Application\Actions\Icon\IconAction:fetch');
      $group->get('/{id}', 'App\Application\Actions\Icon\IconAction:fetchOne');
    });

    $app->group('/api/categories', function (Group $group) {
      $group->get('', 'App\Application\Actions\Category\CategoryAction:fetch');
      $group->get('/{id}', 'App\Application\Actions\Category\CategoryAction:fetchOne');
    });
};
