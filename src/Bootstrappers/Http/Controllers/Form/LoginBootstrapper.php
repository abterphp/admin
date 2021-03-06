<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Http\Controllers\Form;

use AbterPhp\Admin\Http\Controllers\Admin\Form\Login;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

class LoginBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return string[]
     */
    public function getBindings(): array
    {
        return [
            Login::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        $flashService = $container->resolve(FlashService::class);
        $logger       = $container->resolve(LoggerInterface::class);
        $translator   = $container->resolve(ITranslator::class);
        $urlGenerator = $container->resolve(UrlGenerator::class);
        $assets       = $container->resolve(AssetManager::class);
        $frontendSalt = getenv(Env::CRYPTO_FRONTEND_SALT);

        $login = new Login($flashService, $logger, $translator, $urlGenerator, $assets, $frontendSalt);

        $container->bindInstance(Login::class, $login);
    }
}
