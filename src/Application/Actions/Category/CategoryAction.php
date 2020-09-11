<?php

namespace App\Application\Actions\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class CategoryAction
{
    private $em;
    private $category;

    public function __construct(EntityManager $em, Category $category)
    {
        $this->em = $em;
        $this->category = $category;
    }

    public function fetch(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $categories = $this->em->getRepository('App\Entity\Category')->findAll();

        $array_categories = [];
        foreach ($categories as $category) {
          $array_categories[] = $category->getArrayCategory();
        }

        $payload = json_encode($array_categories);

        $response->getBody()->write($payload);
        return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
    }

    public function fetchOne(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if ($category) {
            $payload = json_encode($category->getArrayCategory());
            $response->getBody()->write($payload);
            return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
        }
        throw new HttpNotFoundException($request, 'No category found with id ' . $args['id']);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, $args) {
        $data = $request->getParsedBody();
        if (!isset($data['machine_name'])) {
          $response->getBody()->write('Error during create new category. Missing machine_name.');
          return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)->withHeader('Content-Type', 'application/json');
        }
        $this->category->setMachineName($data['machine_name']);
        if (!isset($data['name'])) {
          $response->getBody()->write('Error during create new category. Missing name.');
          return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)->withHeader('Content-Type', 'application/json');
        }
        $this->category->setName($data['name']);

        $this->em->persist($this->category);
        $this->em->flush();

        $payload = json_encode($this->category->getArrayCategory());
        $response->getBody()->write($payload);
        return $response->withStatus(StatusCodeInterface::STATUS_CREATED)->withHeader('Content-Type', 'application/json');
    }

  public function update(ServerRequestInterface $request, ResponseInterface $response, $args)
  {
    $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
    $category = reset($category);
    if ($category) {
      $request_body = $request->getBody()->getContents();
      $data = json_decode($request_body);
      $category->setMachineName($data->machine_name);
      $category->setName($data->name);
      $this->em->flush();

      $payload = [
        'message' => 'Category (id:' . $args['id'] .') has been updated.',
        'data' => $category->getArrayCategory(),
      ];
      $response->getBody()->write(json_encode($payload));
      return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
    }

    throw new HttpNotFoundException($request, 'No category found with id ' . $args['id']);
  }

  public function delete(ServerRequestInterface $request, ResponseInterface $response, $args) {
    $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
    $category = reset($category);
    if ($category) {
      $this->em->remove($category);
      $this->em->flush();

      $response->getBody()->write('Category (id:' . $args['id'] . ') has been removed.');
      return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
    }
    throw new HttpNotFoundException($request, 'No category found with id ' . $args['id']);
  }

}
