<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\UserAuthLoader;
use Casbin\Exceptions\CasbinException;
use Casbin\Model\Model;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserProviderTest extends TestCase
{
    /** @var UserProvider - System Under Test */
    protected UserProvider $sut;

    /** @var MockObject|UserAuthLoader */
    protected $authLoaderMock;

    public function setUp(): void
    {
        $this->authLoaderMock = $this->createMock(UserAuthLoader::class);

        $this->sut = new UserProvider($this->authLoaderMock);
    }

    public function testLoadPolicyAddsLoadedDataToModel()
    {
        $v00 = 'foo';
        $v01 = 'bar';
        $v10 = 'baz';
        $v11 = 'quix';
        $loadedData = [
            ['v0' => $v00, 'v1' => $v01],
            ['v0' => $v10, 'v1' => $v11],
        ];

        $policyContainer = new \stdClass();
        $policyContainer->policy = [];

        $modelMock = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();
        $modelMock->expects($this->exactly(2))->method('addPolicy');

        $this->authLoaderMock
            ->expects($this->once())
            ->method('loadAll')
            ->willReturn($loadedData);

        $this->sut->loadPolicy($modelMock);
    }

    public function testRemoveFilterPolicyThrowsCasbinException()
    {
        $this->expectException(CasbinException::class);

        $this->sut->removeFilteredPolicy('foo', 'bar', 0);
    }
}
