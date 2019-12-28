<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Admin\Exception\Database;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */

class UserAuthLoader implements IAuthLoader
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
            ->select('u.username AS v0', 'ug.identifier AS v1')
            ->from('users', 'u')
            ->innerJoin('users_user_groups', 'uug', 'uug.user_id = u.id AND uug.deleted_at IS NULL')
            ->innerJoin('user_groups', 'ug', 'uug.user_group_id = ug.id AND ug.deleted_at IS NULL')
            ->where('u.deleted_at IS NULL')
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
