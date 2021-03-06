<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Cell\Sortable;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\Grid\Row\Row;

class HeaderFactory
{
    protected const ACTIONS_CONTENT = 'framework:actions';
    protected const ACTIONS_GROUP   = 'actions';

    /** @var array<string,string> */
    protected array $headers = [];

    /** @var array<string,string> */
    protected array $inputNames = [];

    /** @var array<string,string> */
    protected array $fieldNames = [];

    /**
     * @param bool   $hasActions
     * @param array  $params
     * @param string $baseUrl
     *
     * @return Header
     */
    public function create(bool $hasActions, array $params, string $baseUrl): Header
    {
        $cells = $this->createCells($hasActions);

        $header   = new Header();
        $header[] = new Row($cells);

        $header->setParams($params)->setBaseUrl($baseUrl);

        return $header;
    }

    /**
     * @param bool $hasActions
     *
     * @return Cells
     */
    protected function createCells(bool $hasActions): Cells
    {
        $cells = new Cells();
        foreach ($this->headers as $group => $content) {
            if (!array_key_exists($group, $this->inputNames) || !array_key_exists($group, $this->fieldNames)) {
                $cells[] = new Cell($content, $group, [], [], Html5::TAG_TH);
            } else {
                $cells[] = new Sortable($content, $group, $this->inputNames[$group], $this->fieldNames[$group], [], []);
            }
        }

        if ($hasActions) {
            $cells[] = new Cell(static::ACTIONS_CONTENT, static::ACTIONS_GROUP, [], [], Html5::TAG_TH);
        }

        return $cells;
    }
}
