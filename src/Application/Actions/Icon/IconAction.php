<?php

namespace App\Application\Actions\Icon;

use Doctrine\ORM\EntityManager;
use App\Entity\Icon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Validator;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use App\Entity\Category;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Views\Twig;

class IconAction
{
    private $em;
    private $icon;
    private $validator;
    private $numberValidator;
    private $stringValidator;
    private $srcValidator;
    private $view;
    private $settings;

    public function __construct(EntityManager $em, Icon $icon, Validator $validator, Twig $view, array $settings)
    {
        $this->em = $em;
        $this->icon = $icon;
        $this->validator = $validator;
        $this->view = $view;
        $this->settings = $settings;

        $this->numberValidator = $this->validator::number();
        $this->stringValidator = $this->validator::stringType()->notEmpty()->length(1, 64);
        $this->srcValidator = $this->validator::stringType()->notEmpty();
    }

    public function index(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        $path = explode('/', $request->getUri()->getPath());
        $is_admin = (isset($path[1]) && $path[1] == 'admin');
        $is_api = (isset($path[1]) && $path[1] == 'api');

        if (isset($params['category'])) {
            $category = $this->em->getRepository('App\Entity\Category')->findBy(['machine_name' => $params['category']]);
            if (empty($category)) {
                throw new HttpNotFoundException($request, 'No category found with machine_name: ' . $params['category']);
            }

            $category = reset($category);
            if ($is_api) {
                $icons = $this->em->getRepository('App\Entity\Icon')->findBy(['category' => $category->getId(), 'status' => 1]);
            } else {
                $icons = $this->em->getRepository('App\Entity\Icon')->findBy(['category' => $category->getId()]);
            }
        } else {
            if ($is_api) {
                $icons = $this->em->getRepository('App\Entity\Icon')->findBy(['status' => 1]);
            } else {
                $icons = $this->em->getRepository('App\Entity\Icon')->findAll();
            }
        }

        $array_icons = [];
        foreach ($icons as $icon) {
            $icon = $icon->getArrayIcon();
            $icon_url = $this->settings['base_url'] . '/icon/' . $icon['id'];

            if ($is_admin) {
                $edit_link = $this->settings['base_url'] . '/admin/icons/' . $icon['id'] . '/edit';
                $delete_link = $this->settings['base_url'] . '/admin/icons/' . $icon['id'] . '/delete';

                $icon['view'] = '<a href="' . $icon_url . '" target="_blank"><i class="fas fa-eye"></i></a>';
                $icon['edit'] = '<a href="' . $edit_link . '"><i class="fas fa-edit"></i></a>';
                $icon['delete'] = '<a href="' . $delete_link . '"><i class="fas fa-trash"></i></a>';
            }
            elseif ($is_api) {
                $icon['url'] = $icon_url;
            }
            $array_icons[] = $icon;
        }

        if ($is_api) {
            $payload = json_encode($array_icons);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }

        if ($is_admin) {
            foreach ($array_icons as $icon_key => $icon) {
                $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $icon['category']]);
                $category = reset($category);
                $array_icons[$icon_key]['category'] = $category->getName();
            }

            $categories = $this->em->getRepository('App\Entity\Category')->findAll();
            $array_categories = [];
            foreach ($categories as $category) {
                $array_categories[] = $category->getArrayCategory();
            }

            return $this->view->render($response, 'table.html', [
              'type' => 'icon',
              'title' => 'Icons',
              'data' => $array_icons,
              'categories' => $array_categories,
              'param' => isset($params['category']) ? $params['category'] : null,
            ]);
        }
    }

    public function show(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }
        $icon = $icon->getArrayIcon();
        $icon['url'] = $this->settings['base_url'] . '/icon/' . $icon['id'];
        $payload = json_encode($icon);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response)
    {
        $categories = $this->em->getRepository('App\Entity\Category')->findAll();
        $array_categories = [];
        foreach ($categories as $category) {
            $array_categories[] = $category->getArrayCategory();
        }

        return $this->view->render($response, 'form-icon.html', [
            'title' => 'Create icon',
            'categories' => $array_categories,
            'action' => '/admin/icons',
            'method' => 'POST',
        ]);
    }

    public function edit(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
          throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }

        $icon = $icon->getArrayIcon();
        $categories = $this->em->getRepository('App\Entity\Category')->findAll();
        $array_categories = [];
        foreach ($categories as $category) {
            $array_categories[] = $category->getArrayCategory();
        }

        return $this->view->render($response, 'form-icon.html', [
            'title' => 'Update icon',
            'categories' => $array_categories,
            'icon_data' => $icon,
            'action' => '/admin/icons/' . $args['id'],
            'method' => 'POST',
        ]);
    }

    public function remove(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }

        $icon = $icon->getArrayIcon();

        return $this->view->render($response, 'delete-confirmation.html', [
            'title' => 'Delete icon',
            'name' => 'icon',
            'data' => $icon,
            'action' => '/admin/icons/' . $args['id'],
            'method' => 'POST',
        ]);
    }

    public function store(Request $request, Response $response)
    {
        $data = $request->getParsedBody();

        if (!$this->stringValidator->validate($data['name'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Name must be a non-empty string and maximum of 64 characters in length.');
        }
        if (!$this->numberValidator->validate($data['category'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Category must be a number.');
        }
        if (!$this->srcValidator->validate($data['src'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Src must be a non-empty string or resource of svg file.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $data['category']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $data['category']);
        }

        $this->icon->setName($data['name']);
        $this->icon->assignToCategory($category);
        $this->icon->setSimIconName($data['sim_icon_name']);
        $status = !empty($data['status']) ? $data['status'] : '0';
        $this->icon->setStatus($status);
        $this->icon->setSrc($data['src']);
        $this->em->persist($this->icon);
        $this->em->flush();

        $payload = json_encode($this->icon->getArrayIcon());
        $response->getBody()->write($payload);
        return $response
            ->withStatus(StatusCodeInterface::STATUS_CREATED)
            ->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!$icon) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }

        $data = $request->getParsedBody();

        if (!$this->stringValidator->validate($data['name'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Name must be a non-empty string and maximum of 64 characters in length.');
        }
        if (!$this->numberValidator->validate($data['category'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Category must be a number.');
        }
        if (!$this->srcValidator->validate($data['src'])) {
            throw new HttpBadRequestException($request, 'Wrong data. Src must be a non-empty string or resource of svg file.');
        }

        $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $data['category']]);
        $category = reset($category);
        if (!($category instanceof Category)) {
            throw new HttpNotFoundException($request, 'No category found with id: ' . $data['category']);
        }

        $icon->setName($data['name']);
        $icon->assignToCategory($category);
        $icon->setSimIconName($data['sim_icon_name']);
        $status = !empty($data['status']) ? $data['status'] : '0';
        $icon->setStatus($status);
        $icon->setSrc($data['src']);
        $this->em->flush();

        $payload = [
          'message' => 'Icon (id:' . $args['id'] .') has been updated.',
          'data' => $icon->getArrayIcon(),
        ];
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, $args)
    {
        if (!$this->numberValidator->validate($args['id'])) {
            throw new HttpBadRequestException($request, 'The argument must be a number.');
        }

        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if (!($icon instanceof Icon)) {
            throw new HttpNotFoundException($request, 'No icon found with id: ' . $args['id']);
        }

        $this->em->remove($icon);
        $this->em->flush();

        $response->getBody()->write('Icon (id:' . $args['id'] . ') has been removed.');
        return $response->withHeader('Content-Type', 'application/json');
    }
}
