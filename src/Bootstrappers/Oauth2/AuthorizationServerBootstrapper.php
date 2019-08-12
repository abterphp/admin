<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Oauth2;

use AbterPhp\Admin\Oauth2\Repository;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Crypto\Crypto;
use DateInterval;
use Defuse\Crypto\Key;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Orm\Ids\Generators\UuidV4Generator;

class AuthorizationServerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [AuthorizationServer::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = $container->resolve(ConnectionPool::class);

        /** @var Crypto $crypto */
        $crypto = $container->resolve(Crypto::class);

        /** @var UuidV4Generator $uuidGenerator */
        $uuidGenerator = $container->resolve(UuidV4Generator::class);

        // Init our repositories
        $clientRepository      = new Repository\Client($crypto, $connectionPool);
        $scopeRepository       = new Repository\Scope($connectionPool);
        $accessTokenRepository = new Repository\AccessToken($uuidGenerator, $connectionPool);

        // Path to public and private keys
        $privateKeyPath     = Environment::getVar(Env::OAUTH2_PRIVATE_KEY_PATH);
        $privateKeyPassword = Environment::getVar(Env::OAUTH2_PRIVATE_KEY_PASSWORD);
        $encryptionKeyRaw   = Environment::getVar(Env::OAUTH2_ENCRYPTION_KEY);

        $encryptionKey = Key::loadFromAsciiSafeString($encryptionKeyRaw);
        $privateKey    = new CryptKey($privateKeyPath, $privateKeyPassword);

        // Setup the authorization server
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        $expiry = Environment::getVar(Env::OAUTH2_TOKEN_EXPIRY);
        $server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval($expiry)
        );

        $container->bindInstance(AuthorizationServer::class, $server);
    }
}
