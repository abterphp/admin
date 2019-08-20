<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Grid;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseFactoryTest extends TestCase
{
    /** @var BaseFactory|MockObject - System Under Test */
    protected $sut;

    /** @var UrlGenerator|MockObject */
    protected $urlGeneratorMock;

    /** @var PaginationFactory|MockObject */
    protected $paginationFactoryMock;

    /** @var TableFactory|MockObject */
    protected $tableFactoryMock;

    /** @var GridFactory|MockObject */
    protected $gridFactoryMock;

    /** @var Filters|MockObject */
    protected $filtersMock;

    public function setUp(): void
    {
        $this->urlGeneratorMock      = $this->createMock(UrlGenerator::class);
        $this->paginationFactoryMock = $this->createMock(PaginationFactory::class);
        $this->tableFactoryMock      = $this->createMock(TableFactory::class);
        $this->gridFactoryMock       = $this->createMock(GridFactory::class);
        $this->filtersMock           = $this->createMock(Filters::class);

        $this->sut = $this->getMockForAbstractClass(
            BaseFactory::class,
            [
                $this->urlGeneratorMock,
                $this->paginationFactoryMock,
                $this->tableFactoryMock,
                $this->gridFactoryMock,
                $this->filtersMock,
            ]
        );
    }

    public function testCreateGrid()
    {
        $params  = ['foo' => 'Foo'];
        $baseUrl = '/foo?';

        $this->paginationFactoryMock->expects($this->once())->method('create');
        $this->tableFactoryMock->expects($this->once())->method('create');
        $this->gridFactoryMock->expects($this->once())->method('create');
        $this->filtersMock->expects($this->once())->method('setParams');
        $this->filtersMock->expects($this->once())->method('getUrl');

        $grid = $this->sut->createGrid($params, $baseUrl);

        $this->assertInstanceOf(Grid::class, $grid);
    }
}
