<?php
declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\TwigMiddleware;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\CorsMiddleware;
use App\Domain\Token;

return function (App $app) {
    $app->add(SessionMiddleware::class);
    $app->add(TwigMiddleware::createFromContainer($app));
    
    $app->add(
        new HttpBasicAuthentication([
            "path" => "/token",
            "relaxed" => [],
            "secure" => false,
            "error" => function ($response, $arguments) {
                return $response->withStatus(401);
            },
            "users" => $app->getContainer()->get('settings')['JWTauth']['users']
        ])
    );

    $app->add(new Tuupola\Middleware\JwtAuthentication([
        "path" => "/api",
        "secret" => $app->getContainer()->get('settings')['JWTauth']['secret'],
        "attribute" => false,
        "secure" => false,
        "relaxed" => [],
        "error" => function ($response, $arguments) use ($app) {
          $response->withStatus(401);
          return $app->getContainer()->get('view')->render($response, 'error.html', [
            'status' => 401,
            'title' => 'You are not authorized for selected action.',
            'description' => 'Go to login <a href="/admin">page</a>',
          ]);
        },
        "before" => function ($request, $arguments) {
            $token = new Token([]);
            $token->populate($arguments["decoded"]);
        }
    ]));
    
    $app->add(new CorsMiddleware([
        "origin" => ["*"],
        "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
        "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
        "headers.expose" => ["Authorization", "Etag"],
        "credentials" => true,
        "cache" => 60,
        "error" => function ($request, $response, $arguments) {
            return $response->withStatus(405);
        }
    ]));
};
