<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Grid\Factory\Table\UserGroup as TableFactory;
use AbterPhp\Admin\Grid\Filters\UserGroup as Filters;
use AbterPhp\Framework\Grid\IGrid;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserGroupTest extends TestCase
{
    /** @var UserGroup - System Under Test */
    protected UserGroup $sut;

    /** @var MockObject|UrlGenerator */
    protected $urlGeneratorMock;

    /** @var MockObject|PaginationFactory */
    protected $paginationFactoryMock;

    /** @var MockObject|TableFactory */
    protected $tableFactoryMock;

    /** @var MockObject|GridFactory */
    protected $gridFactoryMock;

    /** @var MockObject|Filters */
    protected $filtersMock;

    /** @var MockObject|IEncrypter */
    protected $encrypterMock;

    public function setUp(): void
    {
        $this->urlGeneratorMock      = $this->createMock(UrlGenerator::class);
        $this->paginationFactoryMock = $this->createMock(PaginationFactory::class);
        $this->tableFactoryMock      = $this->createMock(TableFactory::class);
        $this->gridFactoryMock       = $this->createMock(GridFactory::class);
        $this->filtersMock           = $this->createMock(Filters::class);

        $this->sut = new UserGroup(
            $this->urlGeneratorMock,
            $this->paginationFactoryMock,
            $this->tableFactoryMock,
            $this->gridFactoryMock,
            $this->filtersMock
        );
    }

    public function testCreateGrid()
    {
        $params  = [];
        $baseUrl = '';

        $actualResult = $this->sut->createGrid($params, $baseUrl);

        $this->assertInstanceOf(IGrid::class, $actualResult);
    }
}
