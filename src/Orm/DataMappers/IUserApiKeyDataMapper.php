<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\ApiKey as Entity;
use Opulence\Orm\DataMappers\IDataMapper;

interface IUserApiKeyDataMapper extends IDataMapper
{
    /**
     * @param int      $limitFrom
     * @param int      $pageSize
     * @param string[] $orders
     * @param array    $filters
     * @param array    $params
     *
     * @return Entity[]
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $filters, array $params): array;
}
