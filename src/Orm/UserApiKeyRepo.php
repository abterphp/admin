<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\UserApiKey as Entity;
use AbterPhp\Admin\Orm\DataMappers\UserApiKeySqlDataMapper;
use AbterPhp\Framework\Orm\IGridRepo;
use Opulence\Orm\Repositories\Repository;

class UserApiKeyRepo extends Repository implements IGridRepo
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
        /** @see UserApiKeySqlDataMapper::getPage() */
        return $this->getFromDataMapper('getPage', [$limitFrom, $pageSize, $orders, $conditions, $params]);
    }
}
