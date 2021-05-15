<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\InvalidCsrfTokenException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckCsrfTokenTest extends TestCase
{
    /** @var CheckCsrfToken - System Under Test */
    protected CheckCsrfToken $sut;

    /** @var MockObject|ISession */
    protected $sessionMock;

    /** @var MockObject|CsrfTokenChecker */
    protected $csrfTokenCheckerMock;

    /** @var MockObject|RoutesConfig */
    protected $routesConfigMock;

    public function setUp(): void
    {
        $this->sessionMock          = $this->createMock(ISession::class);
        $this->csrfTokenCheckerMock = $this->createMock(CsrfTokenChecker::class);
        $this->routesConfigMock     = $this->createMock(RoutesConfig::class);

        $this->sut = new CheckCsrfToken(
            $this->csrfTokenCheckerMock,
            $this->sessionMock,
            $this->routesConfigMock
        );
    }

    /**
     * @throws InvalidCsrfTokenException
     */
    public function testHandleSkipsChecksForApiEndpoints()
    {
        $basePath = '/foo';

        $this->routesConfigMock->expects($this->any())->method('getApiBasePath')->willReturn($basePath);

        $this->csrfTokenCheckerMock->expects($this->never())->method('tokenIsValid');

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $requestStub->setPath('/foo/bar');

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    public function testHandleThrowsExceptionOnInvalidToken()
    {
        $this->expectException(InvalidCsrfTokenException::class);

        $basePath = '/foo';

        $this->routesConfigMock->expects($this->any())->method('getApiBasePath')->willReturn($basePath);

        $this->csrfTokenCheckerMock->expects($this->once())->method('tokenIsValid')->willReturn(false);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $requestStub->setPath('/bar');

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $this->sut->handle($requestStub, $next);
    }

    /**
     * @throws InvalidCsrfTokenException
     */
    public function testHandleSetsCookieOnValidToken()
    {
        $basePath = '/foo';

        $this->routesConfigMock->expects($this->any())->method('getApiBasePath')->willReturn($basePath);

        Config::set('sessions', 'cookie.path', '/bar');
        Config::set('sessions', 'cookie.domain', 'example.com');
        Config::set('sessions', 'cookie.isSecure', false);

        $this->csrfTokenCheckerMock->expects($this->once())->method('tokenIsValid')->willReturn(true);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $requestStub->setPath('/bar');

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }
}
