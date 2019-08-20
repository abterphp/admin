<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service;

use AbterPhp\Admin\Databases\Queries\LoginThrottle;
use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Exception\Database;
use AbterPhp\Admin\Orm\LoginAttemptRepo;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Framework\Crypto\Crypto;
use Opulence\Orm\IUnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /** @var UserRepo|MockObject */
    protected $userRepoMock;

    /** @var LoginAttemptRepo|MockObject */
    protected $loginAttemptRepoMock;

    /** @var LoginThrottle|MockObject */
    protected $loginThrottleMock;

    /** @var Crypto|MockObject */
    protected $cryptoMock;

    /** @var IUnitOfWork|MockObject */
    protected $unitOfWorkMock;

    /**
     * @param int  $loginMaxAttempts
     * @param bool $loginLogIp
     *
     * @return Login
     */
    public function createSut(int $loginMaxAttempts, bool $loginLogIp): Login
    {
        $this->userRepoMock         = $this->createMock(UserRepo::class);
        $this->loginAttemptRepoMock = $this->createMock(LoginAttemptRepo::class);
        $this->loginThrottleMock    = $this->createMock(LoginThrottle::class);
        $this->cryptoMock           = $this->createMock(Crypto::class);
        $this->unitOfWorkMock       = $this->createMock(IUnitOfWork::class);

        return new Login(
            $this->userRepoMock,
            $this->loginAttemptRepoMock,
            $this->loginThrottleMock,
            $this->cryptoMock,
            $this->unitOfWorkMock,
            $loginMaxAttempts,
            $loginLogIp
        );
    }

    /**
     * @return array
     */
    public function isLoginAllowedProvider(): array
    {
        return [
            [0, false, 'foo', '127.0.0.1', true],
            [0, false, 'foo', '127.0.0.1', false],
            [0, true, 'foo', '127.0.0.1', true],
            [0, true, 'foo', '127.0.0.1', false],
        ];
    }

    /**
     * @dataProvider isLoginAllowedProvider
     *
     * @param int    $maxAttempts
     * @param bool   $logIp
     * @param string $username
     * @param string $ipAddress
     * @param bool   $expectedResult
     *
     * @throws \Opulence\Orm\OrmException
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function testIsLoginAllowed(
        int $maxAttempts,
        bool $logIp,
        string $username,
        string $ipAddress,
        bool $expectedResult
    ) {
        $sut = $this->createSut($maxAttempts, $logIp);

        $ipHash = $sut->getHash($ipAddress);

        $this->loginThrottleMock
            ->expects($this->once())
            ->method('isLoginAllowed')
            ->with($ipHash, $username, $maxAttempts)
            ->willReturn($expectedResult);

        $actualResult = $sut->isLoginAllowed($username, $ipAddress);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testLoginReturnsNullIfUserIsNotFoundByUsername()
    {
        $username  = 'foo';
        $password  = 'bar';
        $ipAddress = '127.0.0.1';

        $sut = $this->createSut(0, false);

        $this->userRepoMock->expects($this->once())->method('find')->with($username);

        $actualResult = $sut->login($username, $password, $ipAddress);

        $this->assertNull($actualResult);
    }

    public function testLoginReturnsNullIfUserFoundHasNoPassword()
    {
        $user = $this->createUser();
        $user->setPassword('');

        $username  = 'foo';
        $password  = 'bar';
        $ipAddress = '127.0.0.1';

        $sut = $this->createSut(0, false);

        $this->userRepoMock->expects($this->once())->method('find')->with($username)->willReturn($user);

        $actualResult = $sut->login($username, $password, $ipAddress);

        $this->assertNull($actualResult);
    }

    public function testLoginReturnsNullIfUserFoundCanNotLogIn()
    {
        $user = $this->createUser();
        $user->setCanLogin(false);

        $username  = 'foo';
        $password  = 'bar';
        $ipAddress = '127.0.0.1';

        $sut = $this->createSut(0, false);

        $this->userRepoMock->expects($this->once())->method('find')->with($username)->willReturn($user);

        $actualResult = $sut->login($username, $password, $ipAddress);

        $this->assertNull($actualResult);
    }

    public function testLoginReturnsNullIfPasswordIsNotVerified()
    {
        $user = $this->createUser();

        $username  = $user->getUsername();
        $password  = 'bar';
        $ipAddress = '127.0.0.1';

        $sut = $this->createSut(0, false);

        $this->userRepoMock->expects($this->any())->method('find')->willReturn($user);
        $this->cryptoMock
            ->expects($this->once())
            ->method('verifySecret')
            ->with($password, $user->getPassword())
            ->willReturn(false);

        $actualResult = $sut->login($username, $password, $ipAddress);

        $this->assertNull($actualResult);
    }

    public function testLoginThrottleReachedWillLogFailure()
    {
        $user = $this->createUser();

        $username  = $user->getUsername();
        $password  = 'bar';
        $ipAddress = '127.0.0.1';

        $sut = $this->createSut(0, false);

        $this->userRepoMock->expects($this->any())->method('find')->willReturn($user);
        $this->cryptoMock->expects($this->any())->method('verifySecret')->willReturn(true);
        $this->loginThrottleMock->expects($this->any())->method('clear')->willThrowException(new Database());
        $this->loginAttemptRepoMock->expects($this->atLeastOnce())->method('add');
        $this->unitOfWorkMock->expects($this->atLeastOnce())->method('commit');

        $actualResult = $sut->login($username, $password, $ipAddress);

        $this->assertNull($actualResult);
    }

    public function testLoginSuccess()
    {
        $user = $this->createUser();

        $username  = $user->getUsername();
        $password  = 'bar';
        $ipAddress = '127.0.0.1';

        $sut = $this->createSut(0, false);

        $ipHash = $sut->getHash($ipAddress);

        $this->userRepoMock->expects($this->any())->method('find')->willReturn($user);
        $this->cryptoMock->expects($this->any())->method('verifySecret')->willReturn(true);
        $this->loginThrottleMock
            ->expects($this->once())
            ->method('clear')
            ->with($ipHash, $username);

        $actualResult = $sut->login($username, $password, $ipAddress);

        $this->assertSame($user, $actualResult);
    }

    /**
     * @return User
     */
    protected function createUser(): User
    {
        $language = new UserLanguage('lang-1', 'lang-hashtag-1', 'Lang #1');

        return new User('user-1', 'bar', 'baz', 'quix', true, false, $language, []);
    }
}
