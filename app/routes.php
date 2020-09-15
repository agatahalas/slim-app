<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->post('/token', function (Request $request, Response $response) {
      $requested_scopes = $request->getParsedBody() ?: [];

      $valid_scopes = [
          "todo.create",
          "todo.read",
          "todo.update",
          "todo.delete",
          "todo.list",
          "todo.all"
      ];

      $scopes = array_filter($requested_scopes, function ($needle) use ($valid_scopes) {
          return in_array($needle, $valid_scopes);
      });

      $now = new DateTime();
      $future = new DateTime("now +2 hours");
      $server = $request->getServerParams();

      $jti = (new Base62)->encode(random_bytes(16));

      $payload = [
          "iat" => $now->getTimeStamp(),
          "exp" => $future->getTimeStamp()+3600,
          "jti" => $jti,
          "sub" => $server["PHP_AUTH_USER"],
          "scope" => $scopes
      ];

      $secret = $this->get('settings')['JWTauth']['secret'];
      $token = JWT::encode($payload, $secret, "HS256");

      $data["token"] = $token;
      $data["expires"] = $future->getTimeStamp();
      $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
      return $response->withStatus(201)->withHeader("Content-Type", "application/json");
          
  });

  $app->post('/dupa', 'App\Domain\TokenGenerator:getToken');

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->group('/api/icons', function (Group $group) {
      $group->get('', 'App\Application\Actions\Icon\IconAction:index');
      $group->get('/create', 'App\Application\Actions\Icon\IconAction:create');
      $group->get('/{id}', 'App\Application\Actions\Icon\IconAction:show');
      $group->post('', 'App\Application\Actions\Icon\IconAction:store');
      // $group->get('/{$id}/edit', 'App\Application\Actions\Icon\IconAction:update');
      $group->post('/{id}', 'App\Application\Actions\Icon\IconAction:update');
      // $group->get('/{$id}/delete', 'App\Application\Actions\Icon\IconAction:delete');
      $group->delete('/{id}', 'App\Application\Actions\Icon\IconAction:delete');
    });

    $app->group('/api/categories', function (Group $group) {
      $group->get('', 'App\Application\Actions\Category\CategoryAction:index');
      $group->get('/create', 'App\Application\Actions\Category\CategoryAction:create');
      $group->get('/{id}', 'App\Application\Actions\Category\CategoryAction:show');
      $group->post('', 'App\Application\Actions\Category\CategoryAction:store');
      $group->post('/{id}', 'App\Application\Actions\Category\CategoryAction:update');
      $group->delete('/{id}', 'App\Application\Actions\Category\CategoryAction:delete');
    });

    $app->group('/admin', function (Group $group) {
      $group->get('', 'App\Application\Actions\Admin\AdminAction:login');
      $group->get('/login-from-template', 'App\Application\Actions\Admin\AdminAction:loginFromTemplate');
    });
};
