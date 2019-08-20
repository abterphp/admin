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
    protected $sut;

    /** @var MockObject|ISession */
    protected $sessionMock;

    /** @var MockObject|SessionHandlerInterface */
    protected $sessionHandlerMock;

    public function setUp(): void
    {
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

        $this->sessionHandlerMock = $this->getMockBuilder(SessionHandlerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['close', 'destroy', 'gc', 'open', 'read', 'write'])
            ->getMock();

        $this->sut = new Authentication(
            $this->sessionMock,
            $this->sessionHandlerMock
        );
    }

    public function testHandleRedirectsToLoginPathIfUserIsNotLoggedIn()
    {
        $loginPath = '/foo';

        RoutesConfig::setLoginPath($loginPath);

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
