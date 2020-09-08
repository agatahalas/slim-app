<?php
declare(strict_types=1);

namespace App\Domain\Icon;

interface IconRepository
{
    /**
     * @return Icon[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Icon
     * @throws IconNotFoundException
     */
    public function findIconOfId(int $id): Icon;
}
