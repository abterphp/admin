<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Views\Builders;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Navigation\Navigation;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Sessions\ISession;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminBuilderTest extends TestCase
{
    /** @var AdminBuilder - System Under Test */
    protected $sut;

    /** @var MockObject|ISession */
    protected $sessionMock;

    /** @var MockObject|AssetManager */
    protected $assetManagerMock;

    /** @var MockObject|IEventDispatcher */
    protected $eventDispatcherMock;

    /** @var MockObject|Navigation|null */
    protected $primaryNavMock;

    /** @var MockObject|Navigation|null */
    protected $navbarMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->sessionMock         = $this->createMock(ISession::class);
        $this->assetManagerMock    = $this->createMock(AssetManager::class);
        $this->eventDispatcherMock = $this->createMock(IEventDispatcher::class);

        $this->sut = new AdminBuilder(
            $this->sessionMock,
            $this->assetManagerMock,
            $this->eventDispatcherMock,
            $this->primaryNavMock,
            $this->navbarMock
        );
    }

    public function testBuildWorksWithoutNavigation()
    {
        /** @var IView|MockObject $viewMock */
        $viewMock = $this->createMock(IView::class);

        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');

        $actualResult = $this->sut->build($viewMock);

        $this->assertSame($viewMock, $actualResult);
    }

    public function testBuildWorksWithNavigation()
    {
        /** @var IView|MockObject $viewMock */
        $viewMock = $this->createMock(IView::class);

        /** @var Navigation|MockObject $navBarStub */
        $navBarStub = $this->createMock(Navigation::class);

        $this->sut->setNavbar($navBarStub)->setPrimeNav($navBarStub);

        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');

        $actualResult = $this->sut->build($viewMock);

        $this->assertSame($viewMock, $actualResult);
    }
}
