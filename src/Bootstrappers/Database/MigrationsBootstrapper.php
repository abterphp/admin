<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Database;

use AbterPhp\Admin\Bootstrappers\Filesystem\FileFinderBootstrapper;
use AbterPhp\Admin\Databases\Migrations\Init;
use AbterPhp\Framework\Filesystem\IFileFinder; // @phan-suppress-current-line PhanUnreferencedUseNormal
use Opulence\Databases\IConnection; // @phan-suppress-current-line PhanUnreferencedUseNormal
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
        /** @var IConnection $connection */
        $connection = $container->resolve(IConnection::class);

        /** @var IFileFinder $fileFinder */
        $fileFinder = $container->resolve(FileFinderBootstrapper::MIGRATION_FILE_FINDER);

        $migration = new Init($connection, $fileFinder);

        $container->bindInstance(Init::class, $migration);
    }
}
