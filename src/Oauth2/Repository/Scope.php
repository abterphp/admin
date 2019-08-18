<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Admin\Oauth2\Entity\Scope as Entity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\Conditions\ConditionFactory;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */
class Scope implements ScopeRepositoryInterface
{
    /** @var ConnectionPool */
    protected $connectionPool;

    /**
     * Scope constructor.
     *
     * @param ConnectionPool $connectionPool
     */
    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
    }

    /**
     * @param string $identifier
     *
     * @return ScopeEntityInterface
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        $scopeData = $this->queryWithoutUser($identifier);

        $scope = new Entity($scopeData['id']);

        return $scope;
    }

    /**
     * @param string $clientId
     *
     * @return array|bool
     */
    protected function queryWithoutUser(string $clientId)
    {
        $query = (new QueryBuilder())
            ->select('ar.id')
            ->from('admin_resources', 'ar')
            ->where('ar.deleted = 0')
            ->andWhere('ar.identifier = :identifier');

        $sql    = $query->getSql();
        $params = ['identifier' => [$clientId, \PDO::PARAM_STR]];

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Entity[]              $scopes
     * @param string                $grantType
     * @param ClientEntityInterface $clientEntity
     * @param null                  $userIdentifier
     *
     * @return ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        $scopeIds = [];
        foreach ($scopes as $scope) {
            $scopeIds[] = $scope->getIdentifier();
        }

        if (empty($scopeIds)) {
            return [];
        }

        $finalScopes = [];
        foreach ($this->checkScopes($clientEntity->getIdentifier(), $scopeIds) as $scopeData) {
            $finalScopes[] = new Entity($scopeData['admin_resource_id']);
        }

        return $finalScopes;
    }

    /**
     * @param string   $clientId
     * @param string[] $scopeIds
     *
     * @return string[][]
     */
    protected function checkScopes(string $clientId, array $scopeIds): array
    {
        $scopeIdIn = [];
        foreach ($scopeIds as $scopeId) {
            $scopeIdIn[] = [$scopeId, \PDO::PARAM_STR];
        }

        $conditions = new ConditionFactory();
        $query      = (new QueryBuilder())
            ->select('acar.admin_resource_id')
            ->from('api_clients_admin_resources', 'acar')
            ->andWhere('acar.api_client_id = ?')
            ->andWhere($conditions->in('acar.admin_resource_id', $scopeIdIn));

        $sql    = $query->getSql();
        $params = array_merge([[$clientId, \PDO::PARAM_STR]], $scopeIdIn);

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
