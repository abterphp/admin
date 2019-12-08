<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Config;

use AbterPhp\Framework\Exception\Config;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    /** @var Routes - System Under Test */
    protected $sut;

    /** @var string|null */
    protected $origValue;

    /** @var string|null */
    protected $origName;

    public function setUp(): void
    {
        $this->origName  = null;
        $this->origValue = null;

        $this->sut = new Routes();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        if (!$this->origName) {
            return;
        }

        if ($this->origValue === null) {
            putenv($this->origName);
            unset($_ENV[$this->origName]);
            unset($_SERVER[$this->origName]);

            return;
        }

        putenv(sprintf("%s=%s", $this->origName, $this->origValue));
        $_ENV[$this->origName]    = $this->origValue;
        $_SERVER[$this->origName] = $this->origValue;
    }

    protected function newEnvironmentVariable(string $name, ?string $value = null)
    {
        $this->origValue = getenv($name);

        if ($value === null) {
            putenv($name);
            unset($_ENV[$name]);
            unset($_SERVER[$name]);
        } else {
            putenv("$name=$value");
            $_ENV[$name]    = $value;
            $_SERVER[$name] = $value;
        }
    }

    public function testGetLoginPathThrowsExceptionIfRelatedEnvironmentVariableDoesNotExist()
    {
        $this->newEnvironmentVariable(Routes::ADMIN_LOGIN_PATH);

        $this->expectException(Config::class);

        $this->sut->getLoginPath();
    }

    public function testGetLoginPathWillReturnEnvironmentVariableValueByDefault()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::ADMIN_LOGIN_PATH, $envValue);

        $actualResult = $this->sut->getLoginPath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetLoginPathCanReturnEarlyIfAlreadyRun()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::ADMIN_LOGIN_PATH, $envValue);

        $this->sut->getLoginPath();

        // We're removing any environment variable already set
        putenv(Routes::ADMIN_LOGIN_PATH);
        unset($_ENV[Routes::ADMIN_LOGIN_PATH]);
        unset($_SERVER[Routes::ADMIN_LOGIN_PATH]);

        $actualResult = $this->sut->getLoginPath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetLoginPathCanBePreset()
    {
        $envValue    = 'foo';
        $presetValue = 'bar';

        $this->sut->setLoginPath($presetValue);

        $this->newEnvironmentVariable(Routes::ADMIN_LOGIN_PATH, $envValue);

        $this->sut->getLoginPath();

        $actualResult = $this->sut->getLoginPath();

        $this->assertSame($presetValue, $actualResult);
    }

    public function testGetLogoutPathThrowsExceptionIfRelatedEnvironmentVariableDoesNotExist()
    {
        $this->newEnvironmentVariable(Routes::ADMIN_LOGOUT_PATH);

        $this->expectException(Config::class);

        $this->sut->getLogoutPath();
    }

    public function testGetLogoutPathWillReturnEnvironmentVariableValueByDefault()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::ADMIN_LOGOUT_PATH, $envValue);

        $actualResult = $this->sut->getLogoutPath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetLogoutPathCanReturnEarlyIfAlreadyRun()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::ADMIN_LOGOUT_PATH, $envValue);

        $this->sut->getLogoutPath();

        // We're removing any environment variable already set
        putenv(Routes::ADMIN_LOGOUT_PATH);
        unset($_ENV[Routes::ADMIN_LOGOUT_PATH]);
        unset($_SERVER[Routes::ADMIN_LOGOUT_PATH]);

        $actualResult = $this->sut->getLogoutPath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetLogoutPathCanBePreset()
    {
        $envValue    = 'foo';
        $presetValue = 'bar';

        $this->sut->setLogoutPath($presetValue);

        $this->newEnvironmentVariable(Routes::ADMIN_LOGOUT_PATH, $envValue);

        $this->sut->getLogoutPath();

        $actualResult = $this->sut->getLogoutPath();

        $this->assertSame($presetValue, $actualResult);
    }

    public function testGetAdminBasePathThrowExceptionIfRelatedEnvironmentVariableDoesNotExist()
    {
        $this->newEnvironmentVariable(Routes::ADMIN_BASE_PATH);

        $this->expectException(Config::class);

        $this->sut->getAdminBasePath();
    }

    public function testGetAdminBasePathWillReturnEnvironmentVariableValueByDefault()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::ADMIN_BASE_PATH, $envValue);

        $actualResult = $this->sut->getAdminBasePath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetAdminBasePathCanReturnEarlyIfAlreadyRun()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::ADMIN_BASE_PATH, $envValue);

        $this->sut->getAdminBasePath();

        // We're removing any environment variable already set
        putenv(Routes::ADMIN_BASE_PATH);
        unset($_ENV[Routes::ADMIN_BASE_PATH]);
        unset($_SERVER[Routes::ADMIN_BASE_PATH]);

        $actualResult = $this->sut->getAdminBasePath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetAdminBasenPathCanBePreset()
    {
        $envValue    = 'foo';
        $presetValue = 'bar';

        $this->sut->setAdminBasePath($presetValue);

        $this->newEnvironmentVariable(Routes::ADMIN_BASE_PATH, $envValue);

        $actualResult = $this->sut->getAdminBasePath();

        $this->assertSame($presetValue, $actualResult);
    }

    public function testGetApiBasePathThrowExceptionIfRelatedEnvironmentVariableDoesNotExist()
    {
        $this->newEnvironmentVariable(Routes::API_BASE_PATH);

        $this->expectException(Config::class);

        $this->sut->getApiBasePath();
    }

    public function testGetApiBasePathWillReturnEnvironmentVariableValueByDefault()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::API_BASE_PATH, $envValue);

        $actualResult = $this->sut->getApiBasePath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetApiBasePathCanReturnEarlyIfAlreadyRun()
    {
        $envValue = 'foo';

        $this->newEnvironmentVariable(Routes::API_BASE_PATH, $envValue);

        $this->sut->getApiBasePath();

        // We're removing any environment variable already set
        putenv(Routes::API_BASE_PATH);
        unset($_ENV[Routes::API_BASE_PATH]);
        unset($_SERVER[Routes::API_BASE_PATH]);

        $actualResult = $this->sut->getApiBasePath();

        $this->assertSame($envValue, $actualResult);
    }

    public function testGetApiBasenPathCanBePreset()
    {
        $envValue    = 'foo';
        $presetValue = 'bar';

        $this->sut->setApiBasePath($presetValue);

        $this->newEnvironmentVariable(Routes::API_BASE_PATH, $envValue);

        $actualResult = $this->sut->getApiBasePath();

        $this->assertSame($presetValue, $actualResult);
    }

    public function testGetLoginFailurePathContainsAdminPath()
    {
        $adminBasePath = $this->sut->getAdminBasePath();

        $actualResult = $this->sut->getLoginFailurePath();

        $this->assertNotEmpty($adminBasePath);
        $this->assertStringContainsString($adminBasePath, $actualResult);
    }

    public function testGetLoginSuccessPathContainsAdminPath()
    {
        $adminBasePath = $this->sut->getAdminBasePath();

        $actualResult = $this->sut->getLoginSuccessPath();

        $this->assertNotEmpty($adminBasePath);
        $this->assertStringContainsString($adminBasePath, $actualResult);
    }
}
