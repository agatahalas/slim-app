<?php

namespace App\Application\Actions\Category;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;

class CategoryAction
{
    private $em;
    private $category;
    private $validator;
    private $numberValidator;
    private $stringValidator;
    private $view;
    private $settings;

    public function __construct(EntityManager $em, Category $category, Validator $validator, Twig $view, array $settings)
    {
        $this->em = $em;
        $this->category = $category;
        $this->validator = $validator;
        $this->view = $view;
        $this->settings = $settings;

        $this->numberValidator = $this->validator::number();
        $this->stringValidator = $this->validator::stringType()->notEmpty()->length(1, 64);
    }

    public function index(Request $request, Response $response, $args)
    {
        $categories = $this->em->getRepository('App\Entity\Category')->findAll();

        $array_categories = [];
        foreach ($categories as $category) {
            $category = $category->getArrayCategory();
            $edit_link = $this->settings['base_url'] . '/admin/categories/' . $category['id'] . '/edit';
            $delete_link = $this->settings['base_url'] . '/admin/categories/' . $category['id'] . '/delete';

            $category['edit'] = '<a href="' . $edit_link . '"><i class="fas fa-edit"></i></a>';
            $category['delete'] = '<a href="' . $delete_link . '"><i class="fas fa-trash"></i></a>';
            $array_categories[] = $category;
        }

        $path = explode('/', $request->getUri()->getPath());

        if (isset($path[1]) && $path[1] == 'api') {
            $payload = json_encode($array_categories);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        if (isset($path[1]) && $path[1] == 'admin') {
            return $this->view->render($response, 'table.html', [
              'type' => 'category',
              'title' => 'Categories',
              'data' => $array_categories
            ]);
        }
    }

    public function create(Request $request, Response $response)
    {
        return $this->view->render($response, 'form-category.html', [
            'title' => 'Create category',
            'action' => '/admin/categories',
            'method' => 'POST',
        ]);
    }

    public function edit(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $args['id']);
        }

        $category = $category->getArrayCategory();

        return $this->view->render($response, 'form-category.html', [
            'title' => 'Update category',
            'category_data' => $category,
            'action' => '/admin/categories/' . $args['id'],
            'method' => 'POST',
        ]);
    }

    public function remove(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $args['id']);
        }

        $category = $category->getArrayCategory();

        return $this->view->render($response, 'delete-confirmation.html', [
            'title' => 'Delete category',
            'name' => 'category',
            'data' => $category,
            'action' => '/admin/categories/' . $args['id'],
            'method' => 'POST',
        ]);
    }

    public function show(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $args['id']);
        }

        $payload = json_encode($category->getArrayCategory());
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        if (!$this->stringValidator->validate($data['machine_name']) || !$this->stringValidator->validate($data['name'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Machine name and name must be a non-empty string and maximum of 64 characters in length.');
        }

        $this->category->setMachineName($data['machine_name']);
        $this->category->setName($data['name']);
        $this->em->persist($this->category);
        $this->em->flush();

        $payload = json_encode($this->category->getArrayCategory());
        $response->getBody()->write($payload);
        return $response->withStatus(StatusCodeInterface::STATUS_CREATED)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $args['id']);
        }

        $data = $request->getParsedBody();

        if (!$this->stringValidator->validate($data['machine_name']) || !$this->stringValidator->validate($data['name'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Machine name and name must be a non-empty string and maximum of 64 characters in length.');
        }

        $category->setMachineName($data['machine_name']);
        $category->setName($data['name']);
        $this->em->flush();

        $payload = [
          'message' => 'Category (id:' . $args['id'] .') has been updated.',
          'data' => $category->getArrayCategory(),
        ];
        $response->getBody()->write(json_encode($payload));
        return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $args['id']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $args['id']);
        }

        $this->em->remove($category);
        $this->em->flush();

        $response->getBody()->write('Category (id:' . $args['id'] . ') has been removed.');
        return $response->withHeader('Content-Type', 'application/json');
    }
}
