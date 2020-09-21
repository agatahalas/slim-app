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
        //dd($request->getQueryParams());
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }
        $icon = $icon->getArrayIcon();
        $params = $request->getQueryParams();
        if (!empty($params['color'])) {
            $pattern = '/stroke:#[a-f0-9]{6}/m';
            $icon['src'] = preg_replace($pattern, 'stroke:#' . $params['color'], $icon['src']);
            $pattern = '/fill:#[a-f0-9]{6}/m';
            $icon['src'] = preg_replace($pattern, 'fill:#' . $params['color'], $icon['src']);
        }
        $response->getBody()->write($icon['src']);
        return $response->withHeader('Content-Type', 'image/svg+xml');
    }
}
