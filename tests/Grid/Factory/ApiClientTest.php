<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory;

use AbterPhp\Admin\Grid\Factory\Table\ApiClient as TableFactory;
use AbterPhp\Admin\Grid\Filters\ApiClient as Filters;
use AbterPhp\Framework\Grid\IGrid;
use Opulence\Cryptography\Encryption\IEncrypter;
use Opulence\Routing\Urls\UrlGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    /** @var ApiClient - System Under Test */
    protected $sut;

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
        $this->urlGeneratorMock = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->paginationFactoryMock = $this->getMockBuilder(PaginationFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->tableFactoryMock = $this->getMockBuilder(TableFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->gridFactoryMock = $this->getMockBuilder(GridFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->filtersMock = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->encrypterMock = $this->getMockBuilder(IEncrypter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['decrypt', 'encrypt', 'setSecret'])
            ->getMock();

        $this->sut = new ApiClient(
            $this->urlGeneratorMock,
            $this->paginationFactoryMock,
            $this->tableFactoryMock,
            $this->gridFactoryMock,
            $this->filtersMock,
            $this->encrypterMock
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
