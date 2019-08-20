<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Grid;
use AbterPhp\Framework\Grid\Pagination\Pagination;
use AbterPhp\Framework\Grid\Table\Table;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridFactoryTest extends TestCase
{
    public function testCreate()
    {
        $sut = new GridFactory();

        /** @var Table|MockObject $tableMock */
        $tableMock = $this->createMock(Table::class);

        /** @var Pagination|MockObject $paginationMock */
        $paginationMock = $this->createMock(Pagination::class);

        /** @var Filters|MockObject $filtersMock */
        $filtersMock = $this->createMock(Filters::class);

        $grid = $sut->create($tableMock, $paginationMock, $filtersMock, null);

        $this->assertInstanceOf(Grid::class, $grid);
    }
}
