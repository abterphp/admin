<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\AdminResource as Entity;
use Opulence\Orm\Repositories\Repository;

class AdminResourceRepo extends Repository
{
    /**
     * @param string $identifier
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getByIdentifier(string $identifier): ?Entity
    {
        /** @see AdminResourceSqlDataMapper::getByIdentifier() */
        return $this->getFromDataMapper('getByIdentifier', [$identifier]);
    }
    /**
     * @param string $userId
     *
     * @return Entity[]
     * @throws \Opulence\Orm\OrmException
     */
    public function getByUserId(string $userId): array
    {
        /** @see AdminResourceSqlDataMapper::getByUserId() */
        return $this->getFromDataMapper('getByUserId', [$userId]);
    }
}
