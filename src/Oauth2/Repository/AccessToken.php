<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Oauth2\Entity\AccessToken as Entity;
use AbterPhp\Admin\Oauth2\Entity\Scope;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Orm\Ids\Generators\UuidV4Generator;
use Opulence\QueryBuilders\MySql\QueryBuilder;

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
        $data = [
            'id'            => $accessTokenEntity->getIdentifier(),
            'api_client_id' => $accessTokenEntity->getClient()->getName(),
            'expires_at'    => $accessTokenEntity->getExpiryDateTime()->format('Y-m-d H:i:s'),
        ];

        $query = (new QueryBuilder())->insert('tokens', $data);

        $sql    = $query->getSql();
        $params = array_values($data);

        $connection = $this->connectionPool->getWriteConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        $statement->execute();
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     * @param Scope                      $scope
     */
    protected function persistTokenScope(AccessTokenEntityInterface $accessTokenEntity, Scope $scope)
    {
        $data = [
            'id'                => $this->uuidGenerator->generate(null),
            'token_id'          => $accessTokenEntity->getIdentifier(),
            'admin_resource_id' => $scope->getIdentifier(),
        ];

        $query = (new QueryBuilder())->insert('tokens_admin_resources', $data);

        $sql    = $query->getSql();
        $params = array_values($data);

        $connection = $this->connectionPool->getWriteConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        $statement->execute();
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
