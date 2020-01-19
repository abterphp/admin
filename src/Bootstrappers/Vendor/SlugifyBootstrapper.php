<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Vendor;

use Cocur\Slugify\Slugify;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;

class SlugifyBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @inheritdoc
     */
    public function getBindings(): array
    {
        return [Slugify::class];
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $slugify = new Slugify(['regexp' => '/([^A-Za-z0-9_]|-)+/']);

        $container->bindInstance(Slugify::class, $slugify);
    }
}
