<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Filesystem;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Filesystem\FileFinder;
use AbterPhp\Framework\Filesystem\IFileFinder;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem;
use Opulence\Databases\Adapters\Pdo\MySql\Driver as MySqlDriver;
use Opulence\Databases\Adapters\Pdo\PostgreSql\Driver as PostgreSqlDriver;
use Opulence\Environments\Environment;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class FileFinderBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    public const MIGRATION_FILE_FINDER = 'MigrationFileFinder';

    public const MIGRATIONS_PATH_SEGMENT = 'migrations';

    protected ?string $dbDriverName = null;

    /**
     * @return string[]
     */
    public function getBindings(): array
    {
        return [
            IFileFinder::class,
            static::MIGRATION_FILE_FINDER,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $this->registerDefaultFileFinder($container);
        $this->registerMigrationFileFinder($container);
    }

    /**
     * @param IContainer $container
     */
    protected function registerDefaultFileFinder(IContainer $container)
    {
        /** @var IFileFinder $fileFinder */
        $fileFinder = new FileFinder();

        $container->bindInstance(IFileFinder::class, $fileFinder);
    }

    /**
     * @param IContainer $container
     */
    protected function registerMigrationFileFinder(IContainer $container)
    {
        global $abterModuleManager;

        $dbDriver   = $this->getDbDriver();
        $fileFinder = new FileFinder();
        foreach ($abterModuleManager->getResourcePaths() as $resourcePath) {
            $path    = $this->getMigrationsPath($resourcePath, $dbDriver);
            $adapter = new LocalFilesystemAdapter($path);
            $fs      = new Filesystem($adapter);

            $fileFinder->registerFilesystem($fs);
        }

        $container->bindInstance(static::MIGRATION_FILE_FINDER, $fileFinder);
    }

    /**
     * @param string $resourcePath
     * @param string $dbDriver
     *
     * @return string
     */
    protected function getMigrationsPath(string $resourcePath, string $dbDriver): string
    {
        return sprintf(
            '%s%s%s%s%s',
            $resourcePath,
            DIRECTORY_SEPARATOR,
            static::MIGRATIONS_PATH_SEGMENT,
            DIRECTORY_SEPARATOR,
            $dbDriver
        );
    }

    /**
     * @return string
     */
    protected function getDbDriver(): string
    {
        if ($this->dbDriverName !== null) {
            return $this->dbDriverName;
        }

        $driverClass = Environment::getVar(Env::DB_DRIVER) ?: PostgreSqlDriver::class;

        switch ($driverClass) {
            case MySqlDriver::class:
                $this->dbDriverName = 'mysql';
                break;
            case PostgreSqlDriver::class:
                $this->dbDriverName = 'pgsql';
                break;
            default:
                throw new \RuntimeException(
                    "Invalid database driver type specified in environment var \"DB_DRIVER\": $driverClass"
                );
        }

        return $this->dbDriverName;
    }
}
