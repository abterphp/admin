<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Grid\Factory\Table\BodyFactory;
use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Table\Table;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;

class TableFactory
{
    protected const ATTRIBUTE_CLASS = 'class';

    protected const ERROR_MSG_BODY_CREATED      = 'Grid table body is already created.';
    protected const ERROR_MSG_HEADER_CREATED    = 'Grid table header is already created.';
    protected const ERROR_MSG_TABLE_CREATED     = 'Grid table is already created.';
    protected const ERROR_MSG_NO_BODY_CREATED   = 'Grid table body is not yet created';
    protected const ERROR_MSG_NO_HEADER_CREATED = 'Grig table header is not yet created';

    protected HeaderFactory $headerFactory;

    protected BodyFactory $bodyFactory;

    /** @var array<string,Attribute> */
    protected array $tableAttributes = [];

    /** @var array<string,Attribute> */
    protected array $headerAttributes = [];

    /**
     * TableFactory constructor.
     *
     * @param HeaderFactory $headerFactory
     * @param BodyFactory   $bodyFactory
     */
    public function __construct(HeaderFactory $headerFactory, BodyFactory $bodyFactory)
    {
        $this->headerFactory = $headerFactory;
        $this->bodyFactory   = $bodyFactory;

        $tableAttributes       = [self::ATTRIBUTE_CLASS => 'table table-striped table-hover table-bordered'];
        $this->tableAttributes = Attributes::fromArray($tableAttributes);
    }

    /**
     * @param callable[]   $getters
     * @param Actions|null $rowActions
     * @param array        $params
     * @param string       $baseUrl
     *
     * @return Table
     */
    public function create(
        array $getters,
        ?Actions $rowActions,
        array $params,
        string $baseUrl
    ): Table {
        $hasActions = $rowActions && count($rowActions) > 0;

        $header = $this->headerFactory->create($hasActions, $params, $baseUrl);
        $body   = $this->bodyFactory->create($getters, $rowActions);

        return new Table($body, $header, [], $this->tableAttributes);
    }
}
