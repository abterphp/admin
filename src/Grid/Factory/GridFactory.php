<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Grid;
use AbterPhp\Framework\Grid\Pagination\IPagination;
use AbterPhp\Framework\Grid\Table\Table;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;

class GridFactory
{
    protected const ATTRIBUTE_CLASS = 'class';

    /** @var array<string,Attribute> */
    protected array $attributes =  [];

    public function __construct()
    {
        $this->attributes = Attributes::fromArray([self::ATTRIBUTE_CLASS => 'grid']);
    }


    /**
     * @param Table        $table
     * @param IPagination  $pagination
     * @param Filters      $filters
     * @param Actions|null $actions
     *
     * @return Grid
     */
    public function create(Table $table, IPagination $pagination, Filters $filters, ?Actions $actions): Grid
    {
        return new Grid($table, $pagination, $filters, $actions, [], $this->attributes);
    }
}
