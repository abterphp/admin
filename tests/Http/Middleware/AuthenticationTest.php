<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SessionHandlerInterface;

class AuthenticationTest extends TestCase
{
    /** @var Authentication - System Under Test */
    protected Authentication $sut;

    /** @var MockObject|ISession */
    protected $sessionMock;

    /** @var MockObject|SessionHandlerInterface */
    protected $sessionHandlerMock;

    /** @var MockObject|RoutesConfig */
    protected $routesConfigMock;

    public function setUp(): void
    {
        $this->sessionMock = $this->createMock(ISession::class);
        $this->sessionHandlerMock = $this->createMock(SessionHandlerInterface::class);
        $this->routesConfigMock = $this->createMock(RoutesConfig::class);

        $this->sut = new Authentication(
            $this->sessionMock,
            $this->sessionHandlerMock,
            $this->routesConfigMock
        );
    }

    public function testHandleRedirectsToLoginPathIfUserIsNotLoggedIn()
    {
        $loginPath = '/foo';

        $this->routesConfigMock->expects($this->any())->method('getLoginPath')->willReturn($loginPath);

        $this->sessionMock->expects($this->once())->method('has')->willReturn(false);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        /** @var RedirectResponse $actualResult */
        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertNotSame($responseStub, $actualResult);
        $this->assertInstanceOf(RedirectResponse::class, $actualResult);
        $this->assertSame($loginPath, $actualResult->getTargetUrl());
    }

    public function testHandleDoesNothingIfSessionIsSet()
    {
        $this->sessionMock->expects($this->atLeastOnce())->method('has')->willReturn(true);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }
}
