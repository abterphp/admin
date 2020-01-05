<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Factory\IBase;
use AbterPhp\Framework\Grid\IGrid;
use Opulence\Orm\IEntity;
use Opulence\Routing\Urls\UrlGenerator;

abstract class BaseFactory implements IBase
{
    protected const LABEL_EDIT   = 'framework:editItem';
    protected const LABEL_DELETE = 'framework:deleteItem';
    protected const LABEL_VIEW   = 'framework:viewItem';

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var PaginationFactory */
    protected $paginationFactory;

    /** @var TableFactory */
    protected $tableFactory;

    /** @var GridFactory */
    protected $gridFactory;

    /** @var Filters */
    protected $filters;

    /** @var array */
    protected $pageSizeOptions = [];

    /** @var string */
    protected $url;

    /** @var string[] */
    protected $downloadIntents = [Action::INTENT_WARNING];

    /** @var string[] */
    protected $editIntents = [Action::INTENT_PRIMARY];

    /** @var string[] */
    protected $deleteIntents = [Action::INTENT_DANGER];

    /** @var string[] */
    protected $viewIntents = [Action::INTENT_DEFAULT];

    /**
     * Base constructor.
     *
     * @param UrlGenerator      $urlGenerator
     * @param PaginationFactory $paginationFactory
     * @param TableFactory      $tableFactory
     * @param GridFactory       $gridFactory
     * @param Filters|null      $filters
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        PaginationFactory $paginationFactory,
        TableFactory $tableFactory,
        GridFactory $gridFactory,
        Filters $filters = null
    ) {
        $this->urlGenerator      = $urlGenerator;
        $this->paginationFactory = $paginationFactory;
        $this->tableFactory      = $tableFactory;
        $this->gridFactory       = $gridFactory;
        $this->filters           = $filters ?: new Filters();
    }

    /**
     * @param Filters $filters
     *
     * @return IBase
     */
    public function setFilters(Filters $filters): IBase
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @param array  $params
     * @param string $baseUrl
     *
     * @return IGrid
     */
    public function createGrid(array $params, string $baseUrl): IGrid
    {
        $this->filters->setParams($params);

        $filterUrl = $this->filters->getUrl($baseUrl);

        $rowActions  = $this->getRowActions();
        $gridActions = $this->getGridActions();
        $getters     = $this->getGetters();

        $pagination   = $this->paginationFactory->create($params, $filterUrl);
        $paginatedUrl = $pagination->getPageSizeUrl($filterUrl);

        $table     = $this->tableFactory->create($getters, $rowActions, $params, $paginatedUrl);
        $sortedUrl = $table->getSortedUrl($paginatedUrl);
        $pagination->setSortedUrl($sortedUrl);

        $grid = $this->gridFactory->create($table, $pagination, $this->filters, $gridActions);

        return $grid;
    }

    abstract protected function getGetters(): array;

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        $cellActions = new Actions();

        return $cellActions;
    }

    /**
     * @return Actions
     */
    protected function getGridActions(): Actions
    {
        $cellActions = new Actions();

        return $cellActions;
    }

    /**
     * @return callable[]
     */
    protected function getAttributeCallbacks(): array
    {
        $urlGenerator = $this->urlGenerator;

        $hrefClosure = function ($attribute, IEntity $entity) use ($urlGenerator) {
            // @phan-suppress-next-line PhanTypeMismatchArgument
            return $urlGenerator->createFromName($attribute, $entity->getId());
        };

        $attributeCallbacks = [
            Html5::ATTR_HREF => $hrefClosure,
        ];

        return $attributeCallbacks;
    }
}
