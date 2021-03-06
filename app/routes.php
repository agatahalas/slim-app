<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Ramsey\Uuid\Uuid;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->post('/token', 'App\Domain\TokenGenerator:getToken');

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/icon/{id}', 'App\Application\Actions\File\FileAction:show');
    $app->get('/icon/sim-icon/{sim_icon_name}', 'App\Application\Actions\File\FileAction:showBySimIconName');

    $app->group('/api/icons', function (Group $group) {
      $group->get('', 'App\Application\Actions\Icon\IconAction:index');
    });

    $app->group('/admin/icons', function (Group $group) {
        $group->get('', 'App\Application\Actions\Icon\IconAction:index');
        $group->get('/create', 'App\Application\Actions\Icon\IconAction:create');
        $group->get('/{id}', 'App\Application\Actions\Icon\IconAction:show');
        $group->get('/{id}/edit', 'App\Application\Actions\Icon\IconAction:edit');
        $group->get('/{id}/delete', 'App\Application\Actions\Icon\IconAction:remove');
        $group->post('', 'App\Application\Actions\Icon\IconAction:store');
        $group->post('/{id}', 'App\Application\Actions\Icon\IconAction:update');
        $group->delete('/{id}', 'App\Application\Actions\Icon\IconAction:delete');
    });

    $app->group('/admin/categories', function (Group $group) {
        $group->get('', 'App\Application\Actions\Category\CategoryAction:index');
        $group->get('/create', 'App\Application\Actions\Category\CategoryAction:create');
        $group->get('/{id}', 'App\Application\Actions\Category\CategoryAction:show');
        $group->get('/{id}/edit', 'App\Application\Actions\Category\CategoryAction:edit');
        $group->get('/{id}/delete', 'App\Application\Actions\Category\CategoryAction:remove');
        $group->post('', 'App\Application\Actions\Category\CategoryAction:store');
        $group->post('/{id}', 'App\Application\Actions\Category\CategoryAction:update');
        $group->delete('/{id}', 'App\Application\Actions\Category\CategoryAction:delete');
    });

    $app->group('/admin', function (Group $group) {
        $group->get('', 'App\Application\Actions\Admin\AdminAction:login');
        $group->get('/logout', 'App\Application\Actions\Admin\AdminAction:logout');
    });
};

