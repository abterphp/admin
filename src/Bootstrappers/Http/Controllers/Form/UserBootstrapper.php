<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Http\Controllers\Form;

use AbterPhp\Admin\Form\Factory\User as FormFactory;
use AbterPhp\Admin\Http\Controllers\Admin\Form\User;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;

class UserBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            User::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $flashService    = $container->resolve(FlashService::class);
        $translator      = $container->resolve(ITranslator::class);
        $urlGenerator    = $container->resolve(UrlGenerator::class);
        $repo            = $container->resolve(Repo::class);
        $session         = $container->resolve(ISession::class);
        $formFactory     = $container->resolve(FormFactory::class);
        $assets          = $container->resolve(AssetManager::class);
        $eventDispatcher = $container->resolve(IEventDispatcher::class);
        $frontendSalt    = getenv(Env::CRYPTO_FRONTEND_SALT);

        $login = new User(
            $flashService,
            $translator,
            $urlGenerator,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher,
            $assets,
            $frontendSalt
        );

        $container->bindInstance(User::class, $login);
    }
}
