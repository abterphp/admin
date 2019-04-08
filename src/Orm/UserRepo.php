<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Orm\DataMappers\UserSqlDataMapper;
use AbterPhp\Framework\Orm\IGridRepo;
use Opulence\Orm\Repositories\Repository;

class UserRepo extends Repository implements IGridRepo
{
    /**
     * @param int      $limitFrom
     * @param int      $pageSize
     * @param string[] $orders
     * @param array    $conditions
     * @param array    $params
     *
     * @return Entity[]
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $conditions, array $params): array
    {
        /** @see UserSqlDataMapper::getPage() */
        return $this->getFromDataMapper('getPage', [$limitFrom, $pageSize, $orders, $conditions, $params]);
    }

    /**
     * @param string $username
     *
     * @return Entity|null
     */
    public function getByUsername(string $username): ?Entity
    {
        /** @see UserSqlDataMapper::getByUsername() */
        return $this->getFromDataMapper('getByUsername', [$username]);
    }

    /**
     * @param string $email
     *
     * @return Entity|null
     */
    public function getByEmail(string $email): ?Entity
    {
        /** @see UserSqlDataMapper::getByEmail() */
        return $this->getFromDataMapper('getByEmail', [$email]);
    }

    /**
     * @param string $identifier
     *
     * @return Entity|null
     */
    public function find(string $identifier): ?Entity
    {
        /** @see UserSqlDataMapper::find() */
        return $this->getFromDataMapper('find', [$identifier]);
    }
}
