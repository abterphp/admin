<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Views\Builders;

use AbterPhp\Framework\Assets\AssetManager;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Views\IView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginBuilderTest extends TestCase
{
    /** @var LoginBuilder - System Under Test */
    protected $sut;

    /** @var MockObject|AssetManager */
    protected $assetManagerMock;

    /** @var MockObject|IEventDispatcher */
    protected $eventDispatcherMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->assetManagerMock = $this->createMock(AssetManager::class);

        $this->eventDispatcherMock = $this->createMock(IEventDispatcher::class);

        $this->sut = new LoginBuilder(
            $this->assetManagerMock,
            $this->eventDispatcherMock
        );
    }

    public function testBuildWorks()
    {
        /** @var IView|MockObject $viewMock */
        $viewMock = $this->createMock(IView::class);

        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');

        $actualResult = $this->sut->build($viewMock);

        $this->assertSame($viewMock, $actualResult);
    }
}
