<?php

namespace App\Application\Actions\Icon;

use Doctrine\ORM\EntityManager;
use App\Entity\Icon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IconAction
{
    private $em;
    private $icon;

    public function __construct(EntityManager $em, Icon $icon) {
        $this->em = $em;
        $this->icon = $icon;
    }

    public function index($request, $response, $args) {
        $icons = $this->em->getRepository('App\Entity\Icon')->findAll();

        $array_icons = [];
        foreach ($icons as $icon) {
            $array_icons[] = $icon->getArrayIcon();
        }

        $payload = json_encode($array_icons);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show($request, $response, $args) {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if ($icon) {
            $payload = json_encode($icon->getArrayIcon());
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        return $response->withStatus(404, 'No photo found with slug ' . $args['id']);
    }

    public function create(Request $request, Response $response) {
        dd($request->getMethod());
        dd($request->getQueryParams());
        //if ()
        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => 1]);
        $category = reset($category);
        $this->icon->setName('nowa ikona');
        $this->icon->assignToCategory($category);
        $this->icon->setStatus("1");
        $this->icon->setSrc("dupa dupa");
    
        $this->em->persist($this->icon);
        $this->em->flush();

        return $response;
    }

    public function store($request, $response, $args) {

    }
    
    public function update($request, $response, $args) {
        $method = $request->getMethod();
    }

    public function delete($request, $response, $args) {
        $method = $request->getMethod();
    }

}
