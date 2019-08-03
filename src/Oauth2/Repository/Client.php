<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Oauth2\Entity\Client as Entity;
use AbterPhp\Framework\Crypto\Crypto;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\MySql\QueryBuilder;

class Client implements ClientRepositoryInterface
{
    /** @var Crypto */
    protected $crypto;

    /** @var ConnectionPool */
    protected $connectionPool;

    /**
     * Client constructor.
     *
     * @param Crypto         $crypto
     * @param ConnectionPool $connectionPool
     */
    public function __construct(Crypto $crypto, ConnectionPool $connectionPool)
    {
        $this->crypto         = $crypto;
        $this->connectionPool = $connectionPool;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $clientIdentifier
     * @param null   $grantType
     * @param null   $clientSecret
     * @param bool   $mustValidateSecret
     *
     * @return ClientEntityInterface
     */
    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ) {
        $clientData = $this->query($clientIdentifier);

        if (empty($clientData['secret'])) {
            return null;
        }

        $clientSecret = $this->crypto->prepareSecret($clientSecret);
        if (!$this->crypto->verifySecret($clientSecret, $clientData['secret'])) {
            return null;
        }

        $client = new Entity($clientIdentifier, $clientIdentifier, '');

        return $client;
    }

    /**
     * @param string $clientId
     *
     * @return array|bool
     */
    protected function query(string $clientId)
    {
        // TODO: Implement getClientEntity() method.
        $query = (new QueryBuilder())
            ->select('ac.secret')
            ->from('api_clients', 'ac')
            ->where('ac.deleted = 0')
            ->andWhere('ac.id = :clientId');

        $sql    = $query->getSql();
        $params = ['clientId' => $clientId];

        $connection = $this->connectionPool->getReadConnection();
        $statement  = $connection->prepare($sql);
        $statement->bindValues($params);
        if (!$statement->execute()) {
            return false;
        }

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
}
