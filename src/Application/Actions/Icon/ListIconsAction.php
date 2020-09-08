<?php
declare(strict_types=1);

namespace App\Application\Actions\Icon;

use Psr\Http\Message\ResponseInterface as Response;

class ListIconsAction extends IconAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $icons = $this->iconRepository->findAll();

        $this->logger->info("Icon list was viewed.");

        return $this->respondWithData($icons);
    }
}
