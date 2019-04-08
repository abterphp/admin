<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Constant\Routes;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Factory\BaseFactory;
use AbterPhp\Framework\Grid\Factory\GridFactory;
use AbterPhp\Framework\Grid\Factory\PaginationFactory as PaginationFactory;
use AbterPhp\Admin\Grid\Factory\Table\UserGroup as Table;
use AbterPhp\Admin\Grid\Filters\UserGroup as Filters;
use Opulence\Routing\Urls\UrlGenerator;

class UserGroup extends BaseFactory
{
    const GROUP_ID         = 'userGroup-id';
    const GROUP_NAME       = 'userGroup-name';
    const GROUP_IDENTIFIER = 'userGroup-identifier';

    const HEADER_ID         = 'admin:userGroupId';
    const HEADER_NAME       = 'admin:userGroupName';
    const HEADER_IDENTIFIER = 'admin:userGroupIdentifier';

    const GETTER_ID         = 'getId';
    const GETTER_NAME       = 'getName';
    const GETTER_IDENTIFIER = 'getIdentifier';

    /**
     * UserGroup constructor.
     *
     * @param UrlGenerator      $urlGenerator
     * @param PaginationFactory $paginationFactory
     * @param Table             $tableFactory
     * @param GridFactory       $gridFactory
     * @param Filters           $filters
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        PaginationFactory $paginationFactory,
        Table $tableFactory,
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
            static::GROUP_ID         => static::GETTER_ID,
            static::GROUP_IDENTIFIER => static::GETTER_IDENTIFIER,
            static::GROUP_NAME       => static::GETTER_NAME,
        ];
    }

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        $attributeCallbacks = $this->getAttributeCallbacks();

        $editAttributes = [
            Html5::ATTR_HREF  => Routes::ROUTE_USER_GROUPS_EDIT,
        ];

        $deleteAttributes = [
            Html5::ATTR_HREF  => Routes::ROUTE_USER_GROUPS_DELETE,
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
