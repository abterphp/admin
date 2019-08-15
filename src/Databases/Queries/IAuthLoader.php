<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

interface IAuthLoader
{
    /**
     * @return array
     */
    public function loadAll(): array;
}
