<?php

namespace App\Application\Actions\Category;

use Doctrine\ORM\EntityManager;
use Slim\Exception\HttpNotFoundException;

class CategoryAction
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function fetch($request, $response, $args)
    {
        $categories = $this->em->getRepository('App\Entity\Category')->findAll();

        $array_categories = [];
        foreach ($categories as $category) {
          $array_categories[] = $category->getArrayCategory();
        }

        $payload = json_encode($array_categories);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function fetchOne($request, $response, $args)
    {
        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if ($category) {
            $payload = json_encode($category->getArrayCategory());
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        throw new HttpNotFoundException($request, 'No category found with id ' . $args['id']);
    }
}
