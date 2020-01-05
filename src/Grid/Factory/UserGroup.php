<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Constant\Route;
use AbterPhp\Admin\Grid\Factory\Table\Header\UserGroup as HeaderFactory;
use AbterPhp\Admin\Grid\Factory\Table\UserGroup as TableFactory;
use AbterPhp\Admin\Grid\Filters\UserGroup as Filters;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use Opulence\Routing\Urls\UrlGenerator;

class UserGroup extends BaseFactory
{
    private const GETTER_NAME = 'getName';

    /**
     * UserGroup constructor.
     *
     * @param UrlGenerator      $urlGenerator
     * @param PaginationFactory $paginationFactory
     * @param TableFactory      $tableFactory
     * @param GridFactory       $gridFactory
     * @param Filters           $filters
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        PaginationFactory $paginationFactory,
        TableFactory $tableFactory,
        GridFactory $gridFactory,
        Filters $filters
    ) {
        parent::__construct($urlGenerator, $paginationFactory, $tableFactory, $gridFactory, $filters);
    }

    /**
     * @return array
     */
    public function getGetters(): array
    {
        return [
            HeaderFactory::GROUP_NAME => static::GETTER_NAME,
        ];
    }

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        $attributeCallbacks = $this->getAttributeCallbacks();

        $editAttributes = [
            Html5::ATTR_HREF => Route::USER_GROUPS_EDIT,
        ];

        $deleteAttributes = [
            Html5::ATTR_HREF => Route::USER_GROUPS_DELETE,
        ];

        $cellActions   = new Actions();
        $cellActions[] = new Action(
            static::LABEL_EDIT,
            $this->editIntents,
            $editAttributes,
            $attributeCallbacks,
            Html5::TAG_A
        );
        $cellActions[] = new Action(
            static::LABEL_DELETE,
            $this->deleteIntents,
            $deleteAttributes,
            $attributeCallbacks,
            Html5::TAG_A
        );

        return $cellActions;
    }
}
