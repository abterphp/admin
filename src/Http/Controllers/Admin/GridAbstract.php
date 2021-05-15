<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Events\GridReady;
use AbterPhp\Framework\Grid\Factory\IBase as GridFactory;
use AbterPhp\Framework\Grid\Pagination\Options as PaginationOptions;
use AbterPhp\Framework\Http\Service\RepoGrid\IRepoGrid;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Orm\IGridRepo;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

abstract class GridAbstract extends AdminAbstract
{
    public const ENTITY_PLURAL       = '';
    public const ENTITY_TITLE_PLURAL = '';

    public const VIEW_LIST = 'contents/backend/grid';

    public const VAR_GRID       = 'grid';
    public const VAR_CREATE_URL = 'createUrl';

    public const TITLE_SHOW = 'framework:titleList';

    public const URL_CREATE = '%s-create';

    public const RESOURCE_DEFAULT = '%s-grid';
    public const RESOURCE_HEADER  = '%s-header-grid';
    public const RESOURCE_FOOTER  = '%s-footer-grid';
    public const RESOURCE_TYPE    = 'grid';

    protected IGridRepo $gridRepo;

    protected FoundRows $foundRows;

    protected GridFactory $gridFactory;

    protected PaginationOptions $paginationOptions;

    protected AssetManager $assets;

    protected IRepoGrid $repoGrid;

    protected IEventDispatcher $eventDispatcher;

    /**
     * GridAbstract constructor.
     *
     * @param FlashService     $flashService
     * @param LoggerInterface  $logger
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param AssetManager     $assets
     * @param IRepoGrid        $repoGrid
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        AssetManager $assets,
        IRepoGrid $repoGrid,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct($flashService, $logger, $translator, $urlGenerator);

        $this->assets          = $assets;
        $this->repoGrid        = $repoGrid;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return Response
     * @throws \Casbin\Exceptions\CasbinException
     * @throws \Throwable
     */
    public function show(): Response
    {
        $grid = $this->repoGrid->createAndPopulate($this->request->getQuery(), $this->getBaseUrl());

        $this->eventDispatcher->dispatch(Event::GRID_READY, new GridReady($grid));

        $grid->setTranslator($this->translator);

        $title = $this->translator->translate(static::TITLE_SHOW, static::ENTITY_TITLE_PLURAL);

        $this->view = $this->viewFactory->createView(static::VIEW_LIST);
        $this->view->setVar(static::VAR_GRID, $grid);
        $this->view->setVar(static::VAR_CREATE_URL, $this->getCreateUrl());

        $this->addCustomAssets();

        return $this->createResponse($title);
    }

    /**
     * @return Response
     * @throws \Casbin\Exceptions\CasbinException
     * @throws \Throwable
     */
    public function list(): Response
    {
        $grid = $this->repoGrid->createAndPopulate($this->request->getQuery(), $this->getBaseUrl());

        $this->eventDispatcher->dispatch(Event::GRID_READY, new GridReady($grid));

        $grid->setTranslator($this->translator);

        $title = $this->translator->translate(static::TITLE_SHOW, static::ENTITY_TITLE_PLURAL);

        $this->view = $this->viewFactory->createView(static::VIEW_LIST);
        $this->view->setVar(static::VAR_GRID, $grid);
        $this->view->setVar(static::VAR_CREATE_URL, $this->getCreateUrl());

        $this->addCustomAssets();

        return $this->createResponse($title);
    }

    /**
     * @param IStringerEntity|null $entity
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        $this->prepareCustomAssets();

        $this->addTypeAssets();
    }

    protected function addTypeAssets()
    {
        $groupName = $this->getResourceTypeName(static::RESOURCE_FOOTER);

        $this->assets->addJs($groupName, '/admin-assets/js/hideable-container.js');
        $this->assets->addJs($groupName, '/admin-assets/js/filters.js');
        $this->assets->addJs($groupName, '/admin-assets/js/tooltips.js');
        $this->assets->addJs($groupName, '/admin-assets/js/pagination.js');
    }

    /**
     * @return string
     * @throws \Opulence\Routing\Urls\URLException
     */
    protected function getBaseUrl(): string
    {
        return $this->urlGenerator->createFromName(static::ROUTING_PATH) . '?';
    }

    /**
     * @return string
     * @throws \Opulence\Routing\Urls\URLException
     */
    protected function getCreateUrl(): string
    {
        $urlName = sprintf(static::URL_CREATE, static::ROUTING_PATH);

        return $this->urlGenerator->createFromName($urlName);
    }
}
