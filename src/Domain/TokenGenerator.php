<?php

declare(strict_types=1);

namespace App\Domain;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;
use Tuupola\Base62;

class TokenGenerator
{
    private $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function getToken(Request $request, Response $response) {
        $requested_scopes = $request->getParsedBody() ?: [];

        $now = new \DateTime();
        $future = new \DateTime("now +2 hours");
        $server = $request->getServerParams();

        $jti = (new Base62())->encode(random_bytes(16));

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp() + 3600,
            "jti" => $jti,
            "sub" => $server["PHP_AUTH_USER"],
        ];

        $secret = $this->settings['JWTauth']['secret'];
        $token = JWT::encode($payload, $secret, "HS256");

        $data['token'] = $token;
        $data['expires'] = $future->getTimeStamp();
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return $response->withStatus(200)->withHeader("Content-Type", "application/json");

    }
}
