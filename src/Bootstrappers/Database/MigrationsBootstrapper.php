<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Database;

use AbterPhp\Admin\Databases\Migrations\Init;
use AbterPhp\Framework\Constant\Env;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Databases\IConnection;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class MigrationsBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    const MODULE_KEY = 'AbterPhp\\Admin';

    const MIGRATIONS_PATH = '/migrations';

    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            Init::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        $migrationsPath = $this->getMigrationPath();
        $driver         = $this->getDriver();

        /** @var IConnection $connection */
        $connection = $container->resolve(IConnection::class);

        $migration = new Init($connection, $migrationsPath, $driver);

        $container->bindInstance(Init::class, $migration);
    }

    /**
     * @return string
     */
    public function getMigrationPath(): string
    {
        global $abterModuleManager;

        $resourcePaths = $abterModuleManager->getResourcePaths();

        if (empty($resourcePaths[static::MODULE_KEY])) {
            throw new \RuntimeException("Invalid resource path.");
        }

        return $resourcePaths[static::MODULE_KEY] . static::MIGRATIONS_PATH;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        $dirMigrations = getenv(Env::DIR_MIGRATIONS);
        $driverClass   = getenv(Env::DB_DRIVER) ?: PostgreSqlDriver::class;

        switch ($driverClass) {
            case MySqlDriver::class:
                return 'mysql';
            case PostgreSqlDriver::class:
                return 'pgsql';
        }

        throw new \RuntimeException(
            "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
        );
    }
}
