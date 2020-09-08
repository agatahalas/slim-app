<?php
declare(strict_types=1);

namespace App\Domain\Icon;

use App\Domain\DomainException\DomainRecordNotFoundException;

class IconNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The icon you requested does not exist.';
}
