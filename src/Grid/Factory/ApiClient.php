<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Constant\Route;
use AbterPhp\Admin\Grid\Factory\Table\ApiClient as TableFactory;
use AbterPhp\Admin\Grid\Factory\Table\Header\ApiClient as HeaderFactory;
use AbterPhp\Admin\Grid\Filters\ApiClient as Filters;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Html\Helper\Attributes;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Routing\Urls\UrlGenerator;

class ApiClient extends BaseFactory
{
    private const GETTER_ID          = 'getId';
    private const GETTER_DESCRIPTION = 'getDescription';

    /** @var IEncrypter */
    protected IEncrypter $encrypter;

    /**
     * ApiClient constructor.
     *
     * @param UrlGenerator      $urlGenerator
     * @param PaginationFactory $paginationFactory
     * @param TableFactory      $tableFactory
     * @param GridFactory       $gridFactory
     * @param Filters           $filters
     * @param IEncrypter        $encrypter
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        PaginationFactory $paginationFactory,
        TableFactory $tableFactory,
        GridFactory $gridFactory,
        Filters $filters,
        IEncrypter $encrypter
    ) {
        parent::__construct($urlGenerator, $paginationFactory, $tableFactory, $gridFactory, $filters);

        $this->encrypter = $encrypter;
    }

    /**
     * @return array
     */
    public function getGetters(): array
    {
        return [
            HeaderFactory::GROUP_ID          => static::GETTER_ID,
            HeaderFactory::GROUP_DESCRIPTION => static::GETTER_DESCRIPTION,
        ];
    }

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        $attributeCallbacks = $this->getAttributeCallbacks();

        $editAttributes   = Attributes::fromArray([Html5::ATTR_HREF => [Route::API_CLIENTS_EDIT]]);
        $deleteAttributes = Attributes::fromArray([Html5::ATTR_HREF => [Route::API_CLIENTS_DELETE]]);

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
