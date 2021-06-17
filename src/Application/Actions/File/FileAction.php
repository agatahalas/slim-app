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
        return $this->getFileIcon($request, $response, $args['id'], 'id');
    }

    public function showBySimIconName(Request $request, Response $response, $args) {
        return $this->getFileIcon($request, $response, $args['sim_icon_name'], 'sim_icon_name');
    }

    private function getFileIcon(Request $request, Response $response, $field_value, $field_name) {
        $response = $this->cache->withEtag($response, 'show-icon');

        $icon = $this->em->getRepository('App\Entity\Icon')->findBy([$field_name => $field_value]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $field_name);
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
        $resWithExpire = $this->cache->withExpires($response, time() + 31536000)->withHeader('Content-Type', 'image/svg+xml')->withHeader('Cache-Control', 'max-age=31536000');
        return $resWithExpire;
    }
}
