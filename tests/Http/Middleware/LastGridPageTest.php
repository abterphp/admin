<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LastGridPageTest extends TestCase
{
    /** @var LastGridPage - System Under Test */
    protected $sut;

    /** @var MockObject|ISession */
    protected $sessionMock;

    public function setUp(): void
    {
        $this->sessionMock = $this->createMock(ISession::class);

        $this->sut = new LastGridPage($this->sessionMock);
    }

    public function testHandleDoesNotModifySessionIfResponseIsError()
    {
        $this->sessionMock->expects($this->never())->method('set');

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();
        $responseStub->setStatusCode(rand(400, 600));

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    public function testHandleDoesNotModifySessionIfSessionIsNotInitialized()
    {
        $this->sessionMock->expects($this->once())->method('has')->willReturn(false);
        $this->sessionMock->expects($this->never())->method('set');

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    public function testHandleModifiesSessionIfSessionIsInitialized()
    {
        $this->sessionMock->expects($this->any())->method('has')->willReturn(true);
        $this->sessionMock->expects($this->once())->method('set');

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }
}
