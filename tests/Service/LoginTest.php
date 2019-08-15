<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service;

use AbterPhp\Admin\Databases\Queries\LoginThrottle;
use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
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
        $this->userRepoMock = $this->getMockBuilder(UserRepo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock();

        $this->loginAttemptRepoMock = $this->getMockBuilder(LoginAttemptRepo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $this->loginThrottleMock = $this->getMockBuilder(LoginThrottle::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['clear'])
            ->getMock();

        $this->cryptoMock = $this->getMockBuilder(Crypto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['verifySecret'])
            ->getMock();

        $this->unitOfWorkMock = $this->getMockBuilder(IUnitOfWork::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'commit',
                'detach',
                'dispose',
                'getEntityRegistry',
                'registerDataMapper',
                'scheduleForDeletion',
                'scheduleForInsertion',
                'scheduleForUpdate',
                'setConnection',
            ])
            ->getMock();

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
        $this->loginThrottleMock->expects($this->any())->method('clear')->willReturn(false);
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
            ->with($ipHash, $username)
            ->willReturn(true);

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
