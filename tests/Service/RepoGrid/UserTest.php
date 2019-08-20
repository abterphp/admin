<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\RepoGrid;

use AbterPhp\Admin\Grid\Factory\User as GridFactory;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Grid\IGrid;
use Casbin\Enforcer;
use Opulence\Http\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User - System Under Test */
    protected $sut;

    /** @var Enforcer|MockObject */
    protected $enforcerMock;

    /** @var Repo|MockObject */
    protected $repoMock;

    /** @var FoundRows|MockObject */
    protected $foundRowsMock;

    /** @var GridFactory|MockObject */
    protected $gridFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->enforcerMock    = $this->createMock(Enforcer::class);
        $this->repoMock        = $this->createMock(Repo::class);
        $this->foundRowsMock   = $this->createMock(FoundRows::class);
        $this->gridFactoryMock = $this->createMock(GridFactory::class);

        $this->sut = new User(
            $this->enforcerMock,
            $this->repoMock,
            $this->foundRowsMock,
            $this->gridFactoryMock
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
