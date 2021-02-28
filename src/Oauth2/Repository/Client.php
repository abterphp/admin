<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Admin\Oauth2\Entity\Client as Entity;
use AbterPhp\Framework\Crypto\Crypto;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\QueryBuilders\MySql\QueryBuilder;

/** @phan-file-suppress PhanTypeMismatchArgument */
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
     * @param string      $clientIdentifier
     * @param string|null $grantType
     * @param string|null $clientSecret
     * @param bool        $mustValidateSecret
     *
     * @return ClientEntityInterface|null
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

        if ($mustValidateSecret && empty($clientSecret)) {
            return null;
        }

        if ($clientSecret) {
            $clientSecret = $this->crypto->prepareSecret($clientSecret);
            if (!$this->crypto->verifySecret($clientSecret, $clientData['secret'])) {
                return null;
            }
        }

        $client = new Entity($clientIdentifier, $clientIdentifier, '');

        return $client;
    }

    /**
     * @param string $clientId
     *
     * @return array
     */
    protected function query(string $clientId): array
    {
        $query = (new QueryBuilder())
            ->select('ac.secret')
            ->from('api_clients', 'ac')
            ->where('ac.deleted_at IS NULL')
            ->andWhere('ac.id = :clientId');

        $sql    = $query->getSql();
        $params = ['clientId' => [$clientId, \PDO::PARAM_STR]];

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
     * Validate a client's secret.
     *
     * @param string      $clientIdentifier The client's identifier
     * @param null|string $clientSecret     The client's secret (if sent)
     * @param null|string $grantType        The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        // TODO: Improve
        return false;
    }
}
