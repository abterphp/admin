<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Admin\Exception\Database;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */

class LoginThrottle
{
    protected ConnectionPool $connectionPool;

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
     * @param string $ipHash
     * @param string $username
     * @param int    $maxFailureCount
     *
     * @return bool
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function isLoginAllowed(string $ipHash, string $username, int $maxFailureCount): bool
    {
        $query = (new QueryBuilder())
            ->select('COUNT(*) AS count')
            ->from('login_attempts', 'la')
            ->where('la.ip_hash = ? OR la.username = ?')
            ->andWhere('la.created_at > NOW() - INTERVAL 1 HOUR')
            ->addUnnamedPlaceholderValue($ipHash, \PDO::PARAM_STR)
            ->addUnnamedPlaceholderValue($username, \PDO::PARAM_STR);

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }

        return $statement->fetchColumn() < $maxFailureCount;
    }

    /**
     * @param string $ipHash
     * @param string $username
     *
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function clear(string $ipHash, string $username): void
    {
        $query = (new QueryBuilder())
            ->delete('login_attempts')
            ->where('login_attempts.ip_hash = ?')
            ->andWhere('login_attempts.username = ?')
            ->andWhere('login_attempts.created_at > NOW() - INTERVAL 1 HOUR')
            ->addUnnamedPlaceholderValue($ipHash, \PDO::PARAM_STR)
            ->addUnnamedPlaceholderValue($username, \PDO::PARAM_STR);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $connection = $this->connectionPool->getWriteConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);

        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }
    }
}
