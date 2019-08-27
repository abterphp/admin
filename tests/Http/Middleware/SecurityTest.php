<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Framework\Config\EnvReader;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Exception\Security as SecurityException;
use Opulence\Cache\ICacheBridge;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    /** @var Security - System Under Test */
    protected $sut;

    /** @var MockObject|ICacheBridge */
    protected $cacheBridgeMock;

    public function setUp(): void
    {
        $this->cacheBridgeMock = $this->createMock(ICacheBridge::class);

        $this->sut = new Security($this->cacheBridgeMock);
    }

    public function testHandleRunsChecksIfNoEnvironmentNameIsSet()
    {
        (new EnvReader())->clear(Env::ENV_NAME);

        $this->cacheBridgeMock->expects($this->once())->method('has')->willReturn(true);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    public function testHandleSkipsChecksIfNotInProduction()
    {
        (new EnvReader())->set(Env::ENV_NAME, Environment::STAGING);

        $this->cacheBridgeMock->expects($this->never())->method('has');

        $env          = [
            Env::ENV_NAME => Environment::STAGING,
        ];
        $requestStub  = new Request([], [], [], [], [], $env, null);
        $responseStub = new Response();


        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    public function testHandleRunsChecksIfInProduction()
    {
        (new EnvReader())->set(Env::ENV_NAME, Environment::PRODUCTION);

        $this->cacheBridgeMock->expects($this->once())->method('has')->willReturn(true);

        $env          = [
            Env::ENV_NAME => Environment::PRODUCTION,
        ];
        $requestStub  = new Request([], [], [], [], [], $env, null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }

    /**
     * @return string[][]
     */
    public function checksThrowSecurityExceptionProvider(): array
    {
        return [
            [Security::TEST_LOGIN_PATH, '/bar', '/baz', 'quix'],
            ['/foo', Security::TEST_ADMIN_BASE_PATH, '/baz', 'quix'],
            ['/foo', '/bar', Security::TEST_API_BASE_PATH, 'quix'],
            ['/foo', '/bar', '/baz', Security::TEST_OAUTH2_PRIVATE_KEY_PASSWORD],
        ];
    }

    /**
     * @dataProvider checksThrowSecurityExceptionProvider
     *
     * @param string $loginPath
     * @param string $adminBasePath
     * @param string $apiBasePath
     * @param string $oauth2PrivateKeyPassword
     */
    public function testHandleChecksThrowSecurityExceptionOnFailure(
        string $loginPath,
        string $adminBasePath,
        string $apiBasePath,
        string $oauth2PrivateKeyPassword
    ) {
        (new EnvReader())->set(Env::ENV_NAME, Environment::PRODUCTION);

        $this->expectException(SecurityException::class);

        RoutesConfig::setLoginPath($loginPath);
        RoutesConfig::setAdminBasePath($adminBasePath);
        RoutesConfig::setApiBasePath($apiBasePath);

        $this->cacheBridgeMock->expects($this->once())->method('has')->willReturn(false);

        $env          = [
            Env::ENV_NAME                    => Environment::PRODUCTION,
            Env::OAUTH2_PRIVATE_KEY_PASSWORD => $oauth2PrivateKeyPassword,
        ];
        $requestStub  = new Request([], [], [], [], [], $env, null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $this->sut->handle($requestStub, $next);
    }

    public function testHandleSetsSessionIfChecksWereRun()
    {
        $loginPath                = '/foo';
        $adminBasePath            = '/bar';
        $apiBasePath              = '/baz';
        $oauth2PrivateKeyPassword = 'quix';

        RoutesConfig::setLoginPath($loginPath);
        RoutesConfig::setAdminBasePath($adminBasePath);
        RoutesConfig::setApiBasePath($apiBasePath);

        $this->cacheBridgeMock->expects($this->any())->method('has')->willReturn(false);
        $this->cacheBridgeMock->expects($this->once())->method('set')->willReturn(true);

        $env          = [
            Env::ENV_NAME                    => Environment::PRODUCTION,
            Env::OAUTH2_PRIVATE_KEY_PASSWORD => $oauth2PrivateKeyPassword,
        ];
        $requestStub  = new Request([], [], [], [], [], $env, null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
    }
}
