<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Grid;

use AbterPhp\Admin\Service\RepoGrid\UserApiKey as RepoGrid;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Http\Controllers\Admin\GridAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;

class UserApiKey extends GridAbstract
{
    const ENTITY_SINGULAR = 'userApiKey';
    const ENTITY_PLURAL   = 'userApiKeys';

    const ENTITY_TITLE_PLURAL = 'admin:userApiKeys';

    /** @var string */
    protected $resource = 'user_api_keys';

    /**
     * ApiKey constructor.
     *
     * @param FlashService     $flashService
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param AssetManager     $assets
     * @param RepoGrid         $repoGrid
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        AssetManager $assets,
        RepoGrid $repoGrid,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct(
            $flashService,
            $translator,
            $urlGenerator,
            $assets,
            $repoGrid,
            $eventDispatcher
        );
    }
}
