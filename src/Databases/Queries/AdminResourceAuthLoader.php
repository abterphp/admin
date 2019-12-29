<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Admin\Exception\Database;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */

class AdminResourceAuthLoader implements IAuthLoader
{
    /** @var ConnectionPool */
    protected $connectionPool;

    /**
     * BlockCache constructor.
     *
     * @param ConnectionPool $connectionPool
     */
    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @return array
     */
    public function loadAll(): array
    {
        $query = (new QueryBuilder())
            ->select('ug.identifier AS v0', 'ar.identifier AS v1')
            ->from('user_groups_admin_resources', 'ugar')
            ->innerJoin('admin_resources', 'ar', 'ugar.admin_resource_id = ar.id AND ar.deleted_at IS NULL')
            ->innerJoin('user_groups', 'ug', 'ugar.user_group_id = ug.id AND ug.deleted_at IS NULL')
        ;

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
