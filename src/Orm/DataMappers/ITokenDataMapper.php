<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\Token as Entity;
use Opulence\Orm\DataMappers\IDataMapper;

interface ITokenDataMapper extends IDataMapper
{
    /**
     * @param string $clientId
     *
     * @return Entity|null
     */
    public function getByClientId(string $clientId): ?Entity;
}
