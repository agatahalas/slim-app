<?php

namespace App\Application\Actions\File;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use App\Entity\Icon;
use Slim\HttpCache\CacheProvider;

class FileAction
{
    private $em;
    private $cache;

    public function __construct(EntityManager $em, CacheProvider $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    public function show(Request $request, Response $response, $args)
    {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }
        $icon = $icon->getArrayIcon();
        $params = $request->getQueryParams();
        if (!empty($params['color'])) {
            $color_replacement = $params['color'];
            $colors = array_map('str_getcsv', file(__DIR__ . '/lib/colors.csv'));
            foreach ($colors as $color_key => $color_value) {
                if ($color_value['0'] == $params['color']) {
                    $color_replacement = str_replace('#', '', $color_value[2]);
                }
            }

            $pattern = '/stroke:#[a-f0-9]{6}/m';
            $icon['src'] = preg_replace($pattern, 'stroke:#' . $color_replacement, $icon['src']);
            $pattern = '/fill:#[a-f0-9]{6}/m';
            $icon['src'] = preg_replace($pattern, 'fill:#' . $color_replacement, $icon['src']);
        }

        $response->getBody()->write($icon['src']);
        $response->withHeader('Content-Type', 'image/svg+xml');
        $resWithExpire = $this->cache->withExpires($response, time() + 36000);

        return $resWithExpire;
    }
}
