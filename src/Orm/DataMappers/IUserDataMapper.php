<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use Opulence\Orm\DataMappers\IDataMapper;

interface IUserDataMapper extends IDataMapper
{
    /**
     * @param string $clientId
     *
     * @return Entity|null
     */
    public function getByClientId(string $clientId): ?Entity;

    /**
     * @param string $username
     *
     * @return Entity|null
     */
    public function getByUsername(string $username): ?Entity;

    /**
     * @param string $email
     *
     * @return Entity|null
     */
    public function getByEmail(string $email): ?Entity;

    /**
     * @param string $identifier
     *
     * @return Entity|null
     */
    public function find(string $identifier): ?Entity;

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
