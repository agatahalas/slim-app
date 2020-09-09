<?php

namespace App\Application\Actions\Icon;

use Doctrine\ORM\EntityManager;

class IconAction
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function fetch($request, $response, $args)
    {
        $icons = $this->em->getRepository('App\Entity\Icon')->findAll();

        $array_icons = [];
        foreach ($icons as $icon) {
            $array_icons[] = $icon->getArrayIcon();
        }

        $payload = json_encode($array_icons);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchOne($request, $response, $args)
    {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if ($icon) {
            $payload = json_encode($icon->getArrayIcon());
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404, 'No photo found with slug ' . $args['id']);
    }
}
