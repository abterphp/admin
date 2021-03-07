<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Grid;

use AbterPhp\Admin\Http\Controllers\Admin\GridAbstract;
use AbterPhp\Admin\Service\RepoGrid\ApiClient as RepoGrid;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

class ApiClient extends GridAbstract
{
    const ENTITY_SINGULAR = 'apiClient';
    const ENTITY_PLURAL   = 'apiClients';

    const ENTITY_TITLE_PLURAL = 'admin:apiClients';

    const ROUTING_PATH = 'api-clients';

    /** @var string */
    protected $resource = 'api_clients';

    /**
     * ApiClient constructor.
     *
     * @param FlashService     $flashService
     * @param LoggerInterface  $logger
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param AssetManager     $assets
     * @param RepoGrid         $repoGrid
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        AssetManager $assets,
        RepoGrid $repoGrid,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct(
            $flashService,
            $logger,
            $translator,
            $urlGenerator,
            $assets,
            $repoGrid,
            $eventDispatcher
        );
    }
}
