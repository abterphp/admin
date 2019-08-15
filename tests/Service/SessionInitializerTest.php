<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service;

use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Framework\TestDouble\Session\MockSessionFactory;
use PHPUnit\Framework\TestCase;

class SessionInitializerTest extends TestCase
{
    public function testInitializeReturnsEarlyIfAlreadyInitialized()
    {
        $user = $this->createUser();

        $sessionMock = MockSessionFactory::create($this, ['user_id' => 'user-1']);
        $sessionMock->expects($this->never())->method('set');

        $sut = new SessionInitializer($sessionMock);

        $sut->initialize($user);
    }

    public function testInitializeSetsUserSessionIfNotYetInitialized()
    {
        $user = $this->createUser();

        $sessionMock = MockSessionFactory::create($this, ['foo' => 'bar']);
        $sessionMock->expects($this->atLeastOnce())->method('set');

        $sut = new SessionInitializer($sessionMock);

        $sut->initialize($user);
    }

    /**
     * @return User
     */
    protected function createUser(): User
    {
        $language = new UserLanguage('lang-1', 'lang-hashtag-1', 'Lang #1');

        return new User('user-1', 'bar', 'baz', 'quix', false, false, $language, []);
    }
}
