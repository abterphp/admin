<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Http\Controllers\Form;

use AbterPhp\Admin\Form\Factory\ApiClient as FormFactory;
use AbterPhp\Admin\Http\Controllers\Admin\Form\ApiClient;
use AbterPhp\Admin\Orm\ApiClientRepo as Repo;
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
use Psr\Log\LoggerInterface;

class ApiClientBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return string[]
     */
    public function getBindings(): array
    {
        return [
            ApiClient::class,
        ];
    }

    /**
     * @param IContainer $container
     *
     * @throws \Opulence\Ioc\IocException
     */
    public function registerBindings(IContainer $container)
    {
        $flashService    = $container->resolve(FlashService::class);
        $logger          = $container->resolve(LoggerInterface::class);
        $translator      = $container->resolve(ITranslator::class);
        $urlGenerator    = $container->resolve(UrlGenerator::class);
        $repo            = $container->resolve(Repo::class);
        $session         = $container->resolve(ISession::class);
        $formFactory     = $container->resolve(FormFactory::class);
        $eventDispatcher = $container->resolve(IEventDispatcher::class);
        $assets          = $container->resolve(AssetManager::class);
        $secretLength    = getenv(Env::OAUTH2_SECRET_LENGTH);

        $controller = new ApiClient(
            $flashService,
            $logger,
            $translator,
            $urlGenerator,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher,
            $assets,
            $secretLength
        );

        $container->bindInstance(ApiClient::class, $controller);
    }
}
