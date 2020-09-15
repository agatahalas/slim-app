<?php

namespace App\Application\Actions\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class AdminAction {
    private $view;

    public function __construct(Twig $view) {
        $this->view = $view;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, $args) {
        return $this->view->render($response, 'login.html', [
          'name' => 'anything',
        ]);
    }

}
