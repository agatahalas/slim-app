<?php

namespace App\Application\Actions\File;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use App\Entity\Icon;

class FileAction
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function show(Request $request, Response $response, $args)
    {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }
        $icon = $icon->getArrayIcon();
        $response->getBody()->write($icon['src']);
        return $response->withHeader('Content-Type', 'image/svg+xml');
    }
}