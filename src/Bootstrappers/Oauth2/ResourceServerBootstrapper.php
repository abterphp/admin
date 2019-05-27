<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Oauth2;

use AbterPhp\Admin\Oauth2\Repository;
use AbterPhp\Framework\Constant\Env;
use League\OAuth2\Server\ResourceServer;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Orm\Ids\Generators\UuidV4Generator;

class ResourceServerBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [ResourceServer::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        /** @var UuidV4Generator $uuidGenerator */
        $uuidGenerator = $container->resolve(UuidV4Generator::class);

        /** @var ConnectionPool $connectionPool */
        $connectionPool = $container->resolve(ConnectionPool::class);

        // Init our repositories
        $accessTokenRepository = new Repository\AccessToken($uuidGenerator, $connectionPool);

        $publicKeyPath = getenv(Env::OAUTH2_PUBLIC_KEY_PATH);

        $server = new ResourceServer(
            $accessTokenRepository,
            $publicKeyPath
        );

        $container->bindInstance(ResourceServer::class, $server);
    }
}
