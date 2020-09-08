<?php
declare(strict_types=1);

namespace App\Application\Actions\Icon;

use Psr\Http\Message\ResponseInterface as Response;

class ViewIconAction extends IconAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $iconId = (int) $this->resolveArg('id');
        $icon = $this->iconRepository->findIconOfId($iconId);

        $this->logger->info("Icon of id `${$iconId}` was viewed.");

        return $this->respondWithData($icon);
    }
}
