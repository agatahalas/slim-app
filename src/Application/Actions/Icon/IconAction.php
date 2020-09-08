<?php
declare(strict_types=1);

namespace App\Application\Actions\Icon;

use App\Application\Actions\Action;
use App\Domain\Icon\IconRepository;
use Psr\Log\LoggerInterface;

abstract class IconAction extends Action
{
    /**
     * @var IconRepository
     */
    protected $iconRepository;

    /**
     * @param LoggerInterface $logger
     * @param IconRepository  $iconRepository
     */
    public function __construct(LoggerInterface $logger, IconRepository $iconRepository)
    {
        parent::__construct($logger);
        $this->iconRepository = $iconRepository;
    }
}
