<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\RepoGrid;

use AbterPhp\Admin\Grid\Factory\ApiClient as GridFactory;
use AbterPhp\Admin\Orm\ApiClientRepo as Repo;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Grid\IGrid;
use AbterPhp\Framework\TestDouble\Session\MockSessionFactory;
use Casbin\Enforcer;
use Opulence\Http\Collection;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    /** @var ApiClient - System Under Test */
    protected $sut;

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    /** @var Repo|MockObject */
    protected $repoMock;

    /** @var FoundRows|MockObject */
    protected $foundRowsMock;

    /** @var GridFactory|MockObject */
    protected $gridFactoryMock;

    /** @var ISession|MockObject */
    protected $sessionMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->enforcerMock    = $this->createMock(Enforcer::class);
        $this->repoMock        = $this->createMock(Repo::class);
        $this->foundRowsMock   = $this->createMock(FoundRows::class);
        $this->gridFactoryMock = $this->createMock(GridFactory::class);
        $this->sessionMock     = MockSessionFactory::create($this, ['foo' => 'bar']);

        $this->sut = new ApiClient(
            $this->enforcerMock,
            $this->repoMock,
            $this->foundRowsMock,
            $this->gridFactoryMock,
            $this->sessionMock
        );
    }

    public function testCreateAndPopulate()
    {
        $baseUrl = '/foo';

        /** @var Collection|MockObject $query */
        $queryStub = $this->createMock(Collection::class);

        /** @var IGrid|MockObject $query */
        $gridStub = $this->createMock(IGrid::class);

        $this->gridFactoryMock
            ->expects($this->any())
            ->method('createGrid')
            ->willReturn($gridStub);

        $actualResult = $this->sut->createAndPopulate($queryStub, $baseUrl);

        $this->assertSame($gridStub, $actualResult);
    }
}
