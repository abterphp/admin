<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Framework\TestDouble\Session\MockSessionFactory;
use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Sessions\ISession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthorizationTest extends TestCase
{
    protected const RESOURCE = 'foo';
    protected const ROLE     = 'bar';
    protected const USERNAME = 'baz';

    /** @var Authorization - System Under Test */
    protected $sut;

    /** @var MockObject|ISession */
    protected $sessionMock;

    /** @var MockObject|Enforcer */
    protected $enforcerMock;

    /** @var array<string,string> */
    protected $parametersStub = ['resource' => self::RESOURCE, 'role' => self::ROLE];

    /** @var array<string,string> */
    protected $sessionDataStub = ['username' => self::USERNAME];

    public function setUp(): void
    {
        $this->sessionMock = MockSessionFactory::create($this, $this->sessionDataStub);

        $this->enforcerMock = $this->createMock(Enforcer::class);

        $this->sut = new Authorization(
            $this->sessionMock,
            $this->enforcerMock
        );

        $this->sut->setParameters($this->parametersStub);
    }

    public function testHandleCallsNextIfEnforcingCasbinRulesSucceed()
    {
        $this->enforcerMock->expects($this->once())->method('enforce')->willReturn(true);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    public function testHandleRedirectsTo403OnCasbinRulesEnforcingFailure()
    {
        $this->enforcerMock
            ->expects($this->once())
            ->method('enforce')
            ->willReturn(false);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        /** @var RedirectResponse $actualResult */
        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertNotSame($responseStub, $actualResult);
        $this->assertInstanceOf(RedirectResponse::class, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_TEMPORARY_REDIRECT, $actualResult->getStatusCode());
        $this->assertSame(Authorization::PATH_403, $actualResult->getTargetUrl());
    }

    public function testHandleRedirectsTo403OnCasbinRulesEnforcingError()
    {
        $this->enforcerMock
            ->expects($this->once())
            ->method('enforce')
            ->willThrowException(new CasbinException());

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        /** @var RedirectResponse $actualResult */
        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertNotSame($responseStub, $actualResult);
        $this->assertInstanceOf(RedirectResponse::class, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_TEMPORARY_REDIRECT, $actualResult->getStatusCode());
        $this->assertSame(Authorization::PATH_403, $actualResult->getTargetUrl());
    }

    public function testHandleThrowsExceptionOnUnexpectedException()
    {
        $this->expectException(\Exception::class);

        $this->enforcerMock
            ->expects($this->once())
            ->method('enforce')
            ->willThrowException(new \Exception());

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $this->sut->handle($requestStub, $next);
    }
}
