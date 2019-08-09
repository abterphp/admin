<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\Conditions\ConditionFactory;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */

class BlockCache
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
     * @param string[] $identifiers
     * @param string   $cacheTime
     *
     * @return bool
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function hasAnyChangedSince(array $identifiers, string $cacheTime): bool
    {
        $conditions = new ConditionFactory();
        $query      = (new QueryBuilder())
            ->select('COUNT(*) AS count')
            ->from('blocks')
            ->where('blocks.deleted = 0')
            ->leftJoin('block_layouts', 'layouts', 'layouts.id = blocks.layout')
            ->andWhere($conditions->in('blocks.identifier', $identifiers))
            ->andWhere('blocks.updated_at > ? OR layouts.updated_at > ?')
            ->addUnnamedPlaceholderValue($cacheTime, \PDO::PARAM_STR)
            ->addUnnamedPlaceholderValue($cacheTime, \PDO::PARAM_STR);

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        if (!$statement->execute()) {
            return true;
        }

        return $statement->fetchColumn() > 0;
    }
}
