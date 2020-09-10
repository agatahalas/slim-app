<?php

namespace App\Application\Actions\Category;

use App\Entity\Category;
use Cassandra\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Fig\Http\Message\StatusCodeInterface;
use http\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
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

    public function create(ServerRequestInterface $request, ResponseInterface $response, $args) {
        $data = $request->getParsedBody();
        $category = new Category();
        if (!isset($data['machine_name'])) {
          $response->getBody()->write('Error during create new category. Missing machine_name.');
          return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)->withHeader('Content-Type', 'application/json');
        }
        $category->setMachineName($data['machine_name']);
        if (!isset($data['name'])) {
          $response->getBody()->write('Error during create new category. Missing name.');
          return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)->withHeader('Content-Type', 'application/json');
        }
        $category->setName($data['name']);

        $this->em->persist($category);
        $this->em->flush();

        $payload = json_encode($category->getArrayCategory());
        $response->getBody()->write($payload);
        return $response->withStatus(StatusCodeInterface::STATUS_CREATED)->withHeader('Content-Type', 'application/json');
    }

}
