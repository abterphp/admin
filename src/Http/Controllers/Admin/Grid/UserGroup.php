<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Grid;

use AbterPhp\Admin\Http\Controllers\Admin\GridAbstract;
use AbterPhp\Admin\Service\RepoGrid\UserGroup as RepoGrid;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

class UserGroup extends GridAbstract
{
    public const ENTITY_SINGULAR = 'userGroup';
    public const ENTITY_PLURAL   = 'userGroups';

    public const ENTITY_TITLE_PLURAL = 'admin:userGroups';

    public const ROUTING_PATH = 'user-groups';

    protected string $resource = 'user_groups';

    /**
     * UserGroup constructor.
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
