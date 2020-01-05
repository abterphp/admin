<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Constant\Route;
use AbterPhp\Admin\Grid\Factory\Table\Header\User as HeaderFactory;
use AbterPhp\Admin\Grid\Factory\Table\User as TableFactory;
use AbterPhp\Admin\Grid\Filters\User as Filters;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use Opulence\Routing\Urls\UrlGenerator;

class User extends BaseFactory
{
    private const GETTER_USERNAME = 'getUsername';
    private const GETTER_EMAIL    = 'getEmail';

    /**
     * User constructor.
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
            HeaderFactory::GROUP_USERNAME => static::GETTER_USERNAME,
            HeaderFactory::GROUP_EMAIL    => static::GETTER_EMAIL,
        ];
    }

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        $attributeCallbacks = $this->getAttributeCallbacks();

        $editAttributes = [
            Html5::ATTR_HREF => [Route::USERS_EDIT],
        ];

        $deleteAttributes = [
            Html5::ATTR_HREF => [Route::USERS_DELETE],
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
