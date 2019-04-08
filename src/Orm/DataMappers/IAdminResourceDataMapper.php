<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\AdminResource as Entity;
use Opulence\Orm\DataMappers\IDataMapper;

interface IAdminResourceDataMapper extends IDataMapper
{
    /**
     * @param string $identifier
     *
     * @return Entity|null
     */
    public function getByIdentifier(string $identifier): ?Entity;
}
