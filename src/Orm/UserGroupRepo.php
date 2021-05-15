<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Framework\Orm\IGridRepo;
use Opulence\Orm\OrmException;
use Opulence\Orm\Repositories\Repository;

class UserGroupRepo extends Repository implements IGridRepo
{
    /**
     * @param int      $limitFrom
     * @param int      $pageSize
     * @param string[] $orders
     * @param array    $conditions
     * @param array    $params
     *
     * @return Entity[]
     * @throws OrmException
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $conditions, array $params): array
    {
        /** @see UserGroupSqlDataMapper::getPage() */
        return $this->getFromDataMapper('getPage', [$limitFrom, $pageSize, $orders, $conditions, $params]);
    }

    /**
     * @param string $identifier
     *
     * @return Entity|null
     * @throws OrmException
     */
    public function getByIdentifier(string $identifier): ?Entity
    {
        /** @see UserGroupSqlDataMapper::getByIdentifier() */
        return $this->getFromDataMapper('getByIdentifier', [$identifier]);
    }
}
