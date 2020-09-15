<?php
declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\TwigMiddleware;
use Tuupola\Middleware\HttpBasicAuthentication;
use Tuupola\Middleware\CorsMiddleware;
use Gofabian\Negotiation\NegotiationMiddleware;
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
        "error" => function ($response, $arguments) {
            return $response->withStatus(401);
        },
        "before" => function ($request, $arguments) use ($app) {
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
