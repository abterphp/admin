<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Orm;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\LoginAttempt;
use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserApiKey;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Admin\Orm\DataMappers\AdminResourceSqlDataMapper;
use AbterPhp\Admin\Orm\DataMappers\LoginAttemptSqlDataMapper;
use AbterPhp\Admin\Orm\DataMappers\UserApiKeyGroupSqlDataMapper;
use AbterPhp\Admin\Orm\DataMappers\UserGroupSqlDataMapper;
use AbterPhp\Admin\Orm\DataMappers\UserLanguageSqlDataMapper;
use AbterPhp\Admin\Orm\DataMappers\UserSqlDataMapper;
use AbterPhp\Admin\Orm\LoginAttemptRepo;
use AbterPhp\Admin\Orm\UserApiKeyRepo;
use AbterPhp\Admin\Orm\UserGroupRepo;
use AbterPhp\Admin\Orm\UserLanguageRepo;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Framework\Orm\Ids\Generators\IdGeneratorRegistry;
use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Databases\IConnection;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Orm\ChangeTracking\ChangeTracker;
use Opulence\Orm\ChangeTracking\IChangeTracker;
use Opulence\Orm\EntityRegistry;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;
use Opulence\Orm\Ids\Generators\IIdGeneratorRegistry;
use Opulence\Orm\IUnitOfWork;
use Opulence\Orm\UnitOfWork;
use RuntimeException;

/**
 * Defines the ORM bootstrapper
 */
class OrmBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /** @var array */
    protected $repoMappers = [
        AdminResourceRepo::class => [AdminResourceSqlDataMapper::class, AdminResource::class],
        LoginAttemptRepo::class  => [LoginAttemptSqlDataMapper::class, LoginAttempt::class],
        UserApiKeyRepo::class    => [UserApiKeyGroupSqlDataMapper::class, UserApiKey::class],
        UserGroupRepo::class     => [UserGroupSqlDataMapper::class, UserGroup::class],
        UserLanguageRepo::class  => [UserLanguageSqlDataMapper::class, UserLanguage::class],
        UserRepo::class          => [UserSqlDataMapper::class, User::class],
    ];

    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        $baseBindings = [
            IChangeTracker::class,
            IIdAccessorRegistry::class,
            IIdGeneratorRegistry::class,
            IUnitOfWork::class,
        ];

        return array_merge($baseBindings, array_keys($this->repoMappers));
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        try {
            $idAccessorRegistry  = new IdAccessorRegistry();
            $idGeneratorRegistry = new IdGeneratorRegistry();
            $changeTracker       = new ChangeTracker();
            $entityRegistry      = new EntityRegistry($idAccessorRegistry, $changeTracker);
            $unitOfWork          = new UnitOfWork(
                $entityRegistry,
                $idAccessorRegistry,
                $idGeneratorRegistry,
                $changeTracker,
                $container->resolve(IConnection::class)
            );
            $this->bindRepositories($container, $unitOfWork);
            $container->bindInstance(IIdAccessorRegistry::class, $idAccessorRegistry);
            $container->bindInstance(IIdGeneratorRegistry::class, $idGeneratorRegistry);
            $container->bindInstance(IChangeTracker::class, $changeTracker);
            $container->bindInstance(IUnitOfWork::class, $unitOfWork);
            $container->bindInstance(EntityRegistry::class, $entityRegistry);
        } catch (IocException $ex) {
            throw new RuntimeException('Failed to register ORM bindings', 0, $ex);
        }
    }

    /**
     * Binds repositories to the container
     *
     * @param IContainer  $container  The container to bind to
     * @param IUnitOfWork $unitOfWork The unit of work to use in repositories
     */
    protected function bindRepositories(IContainer $container, IUnitOfWork $unitOfWork)
    {
        $connectionPool  = $container->resolve(ConnectionPool::class);
        $readConnection  = $connectionPool->getReadConnection();
        $writeConnection = $connectionPool->getWriteConnection();

        foreach ($this->repoMappers as $repoClass => $classes) {
            $container->bindFactory(
                $repoClass,
                $this->createFactory(
                    $repoClass,
                    $classes[0],
                    $classes[1],
                    $readConnection,
                    $writeConnection,
                    $unitOfWork
                )
            );
        }
    }

    /**
     * @param string      $repoClass
     * @param string      $dataMapperClass
     * @param string      $entityClass
     * @param IConnection $readConnection
     * @param IConnection $writeConnection
     * @param IUnitOfWork $unitOfWork
     *
     * @return \Closure
     */
    private function createFactory(
        string $repoClass,
        string $dataMapperClass,
        string $entityClass,
        IConnection $readConnection,
        IConnection $writeConnection,
        IUnitOfWork $unitOfWork
    ) {
        return function () use (
            $repoClass,
            $dataMapperClass,
            $entityClass,
            $readConnection,
            $writeConnection,
            $unitOfWork
        ) {
            $dataMapper = new $dataMapperClass($readConnection, $writeConnection);

            return new $repoClass($entityClass, $dataMapper, $unitOfWork);
        };
    }
}
