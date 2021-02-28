<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Admin\Oauth2\Entity\AccessToken as Entity;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Orm\Ids\Generators\UuidV4Generator;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */
class AccessToken implements AccessTokenRepositoryInterface
{
    /** @var UuidV4Generator */
    protected $uuidGenerator;

    /** @var ConnectionPool */
    protected $connectionPool;

    /**
     * AccessToken constructor.
     *
     * @param UuidV4Generator $uuidGenerator
     * @param ConnectionPool  $connectionPool
     */
    public function __construct(UuidV4Generator $uuidGenerator, ConnectionPool $connectionPool)
    {
        $this->uuidGenerator  = $uuidGenerator;
        $this->connectionPool = $connectionPool;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param ClientEntityInterface $clientEntity
     * @param array                 $scopes
     * @param null                  $userIdentifier
     *
     * @return AccessTokenEntityInterface
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new Entity();

        return $accessToken;
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->persistToken($accessTokenEntity);

        foreach ($accessTokenEntity->getScopes() as $scope) {
            $this->persistTokenScope($accessTokenEntity, $scope);
        }
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     */
    protected function persistToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $tokenId    = $accessTokenEntity->getIdentifier();
        $clientName = $accessTokenEntity->getClient()->getName();
        $expiresAt  = $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s');

        $columnNamesToValues = [
            'id'            => [$tokenId, \PDO::PARAM_STR],
            'api_client_id' => [$clientName, \PDO::PARAM_STR],
            'expires_at'    => [$expiresAt, \PDO::PARAM_STR],
        ];

        $query = (new QueryBuilder())->insert('tokens', $columnNamesToValues);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $connection = $this->connectionPool->getWriteConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @param ScopeEntityInterface       $scope
     */
    protected function persistTokenScope(AccessTokenEntityInterface $accessTokenEntity, ScopeEntityInterface $scope)
    {
        $scopeId         = $this->uuidGenerator->generate(new \stdClass());
        $tokenId         = $accessTokenEntity->getIdentifier();
        $adminResourceId = $scope->getIdentifier();

        $columnNamesToValues = [
            'id'                => [$scopeId, \PDO::PARAM_STR],
            'token_id'          => [$tokenId, \PDO::PARAM_STR],
            'admin_resource_id' => [$adminResourceId, \PDO::PARAM_STR],
        ];

        $query = (new QueryBuilder())->insert('tokens_admin_resources', $columnNamesToValues);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $connection = $this->connectionPool->getWriteConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        if (!$statement->execute()) {
            throw new Database($statement->errorInfo());
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $tokenId
     *
     * @return bool
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return false;
    }
}
