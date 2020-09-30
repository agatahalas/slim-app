<?php

namespace App\Application\Actions\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Doctrine\ORM\EntityManager;

class AdminAction
{
    private $view;
    private $em;
    private $settings;

    public function __construct(Twig $view, EntityManager $em, array $settings)
    {
        $this->view = $view;
        $this->em = $em;
        $this->settings = $settings;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $number_of_icons = $this->em->getRepository('App\Entity\Icon')->count([]);
        $number_of_categories = $this->em->getRepository('App\Entity\Category')->count([]);
        return $this->view->render($response, 'login.html', [
          'number_of_icons' => $number_of_icons,
          'number_of_categories' => $number_of_categories,
          'base_url' => $this->settings['base_url'],
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
