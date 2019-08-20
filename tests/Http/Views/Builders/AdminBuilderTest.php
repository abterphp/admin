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

        $this->sessionMock = $this->getMockBuilder(ISession::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'ageFlashData',
                    'delete',
                    'flash',
                    'flush',
                    'get',
                    'getAll',
                    'getId',
                    'getName',
                    'has',
                    'hasStarted',
                    'reflash',
                    'regenerateId',
                    'set',
                    'setId',
                    'setMany',
                    'setName',
                    'start',
                    'offsetExists',
                    'offsetGet',
                    'offsetSet',
                    'offsetUnset',
                ]
            )
            ->getMock();

        $this->assetManagerMock = $this->getMockBuilder(AssetManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addJs'])
            ->getMock();

        $this->eventDispatcherMock = $this->getMockBuilder(IEventDispatcher::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['dispatch'])
            ->getMock();

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
        $viewMock = $this->getMockBuilder(IView::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getContents',
                'getDelimiters',
                'getPath',
                'getVar',
                'getVars',
                'hasVar',
                'setContents',
                'setDelimiters',
                'setPath',
                'setVar',
                'setVars',
            ])
            ->getMock();

        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');

        $actualResult = $this->sut->build($viewMock);

        $this->assertSame($viewMock, $actualResult);
    }

    public function testBuildWorksWithNavigation()
    {
        /** @var IView|MockObject $viewMock */
        $viewMock = $this->getMockBuilder(IView::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getContents',
                'getDelimiters',
                'getPath',
                'getVar',
                'getVars',
                'hasVar',
                'setContents',
                'setDelimiters',
                'setPath',
                'setVar',
                'setVars',
            ])
            ->getMock();

        /** @var Navigation|MockObject $navBarStub */
        $navBarStub = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->sut->setNavbar($navBarStub)->setPrimeNav($navBarStub);

        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');

        $actualResult = $this->sut->build($viewMock);

        $this->assertSame($viewMock, $actualResult);
    }
}
