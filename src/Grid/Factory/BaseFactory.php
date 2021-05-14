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

    protected UrlGenerator $urlGenerator;

    protected PaginationFactory $paginationFactory;

    protected TableFactory $tableFactory;

    protected GridFactory $gridFactory;

    protected Filters $filters;

    /** @var array */
    protected array $pageSizeOptions = [];

    /** @var string */
    protected string $url;

    /** @var string[] */
    protected array $downloadIntents = [Action::INTENT_WARNING];

    /** @var string[] */
    protected array $editIntents = [Action::INTENT_PRIMARY];

    /** @var string[] */
    protected array $deleteIntents = [Action::INTENT_DANGER];

    /** @var string[] */
    protected array $viewIntents = [Action::INTENT_DEFAULT];

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

        return $this->gridFactory->create($table, $pagination, $this->filters, $gridActions);
    }

    abstract protected function getGetters(): array;

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        return new Actions();
    }

    /**
     * @return Actions
     */
    protected function getGridActions(): Actions
    {
        return new Actions();
    }

    /**
     * @return array<string,callable>
     */
    protected function getAttributeCallbacks(): array
    {
        $urlGenerator = $this->urlGenerator;

        $hrefClosure = function ($attribute, IEntity $entity) use ($urlGenerator) {
            // @phan-suppress-next-line PhanTypeMismatchArgument
            return $urlGenerator->createFromName($attribute, $entity->getId());
        };

        return [
            Html5::ATTR_HREF => $hrefClosure,
        ];
    }
}
