<?php

namespace App\Application\Actions\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class AdminAction
{
    private $view;

    public function __construct(Twig $view) {
        $this->view = $view;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $logged_in = false;
        if (!empty($_COOKIE['token'])) {
            $logged_in = true;
        }
        return $this->view->render($response, 'login.html', [
          'logged_in' => $logged_in,
        ]);
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        if (!empty($_COOKIE['token'])) {
            setcookie('token', '', time() - 3600, '/');
        }
        return $this->view->render($response, 'logout.html');
    }
}
