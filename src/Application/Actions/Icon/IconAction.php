<?php

namespace App\Application\Actions\Icon;

use Doctrine\ORM\EntityManager;
use App\Entity\Icon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use App\Entity\Category;
use Fig\Http\Message\StatusCodeInterface;

class IconAction
{
    private $em;
    private $icon;

    public function __construct(EntityManager $em, Icon $icon) {
        $this->em = $em;
        $this->icon = $icon;
    }

    public function index(Request $request, Response $response, $args) {
        $icons = $this->em->getRepository('App\Entity\Icon')->findAll();

        $array_icons = [];
        foreach ($icons as $icon) {
            $array_icons[] = $icon->getArrayIcon();
        }

        $payload = json_encode($array_icons);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function show(Request $request, Response $response, $args) {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if ($icon) {
            $payload = json_encode($icon->getArrayIcon());
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
        throw new HttpNotFoundException($request, 'No icon found with id ' . $args['id']);
    }

    public function create(Request $request, Response $response) {

    }

    public function store(Request $request, Response $response) {
        $data = $request->getParsedBody();
        if (!empty($data['name'] && !empty($data['category'] && $data['src']))) {
            $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $data['category']]);
            $category = reset($category);
            if ($category instanceof Category) {
                $this->icon->setName($data['name']);
                $this->icon->assignToCategory($category);
                $status = !empty($data['status']) ? $data['status'] : '0';
                $this->icon->setStatus($status);
                $this->icon->setSrc($data['src']);
                $this->em->persist($this->icon);
                $this->em->flush();
                return $response->withStatus(StatusCodeInterface::STATUS_CREATED)->withHeader('Content-Type', 'application/json');
            }
        }
        $response->getBody()->write("Data is missing or invalid");
        return $response->withStatus(StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, $args) {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if ($icon) {
            $request_body = $request->getParsedBody();
            $icon->setName($request_body['name']);
            $category = $this->em->getRepository('App\Entity\Category')->findBy(['id' => $request_body['category']]);
            $category = reset($category);
            $icon->assignToCategory($category);
            $icon->setStatus($request_body['status']);
            $icon->setSrc($request_body['src']);
            $this->em->flush();

            $payload = [
                'message' => 'Icon (id:' . $args['id'] .') has been updated.',
                'data' => $icon->getArrayIcon(),
            ];
            $response->getBody()->write(json_encode($payload));
            return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
        }

        throw new HttpNotFoundException($request, 'No icon found with id ' . $args['id']);
    }

    public function delete(Request $request, Response $response, $args) {
        $icon = $this->em->getRepository('App\Entity\Icon')->findBy(['id' => $args['id']]);
        $icon = reset($icon);
        if ($icon instanceof Icon) {
            $this->em->remove($icon);
            $this->em->flush();

            $response->getBody()->write('Icon (id:' . $args['id'] . ') has been removed.');
            return $response->withStatus(StatusCodeInterface::STATUS_OK)->withHeader('Content-Type', 'application/json');
        }
        throw new HttpNotFoundException($request, 'No icon found with id ' . $args['id']);
    }

}
