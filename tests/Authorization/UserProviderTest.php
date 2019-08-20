<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\UserAuthLoader;
use Casbin\Exceptions\CasbinException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserProviderTest extends TestCase
{
    /** @var UserProvider - System Under Test */
    protected $sut;

    /** @var MockObject|UserAuthLoader */
    protected $authLoaderMock;

    public function setUp(): void
    {
        $this->authLoaderMock = $this->createMock(UserAuthLoader::class);

        $this->sut = new UserProvider($this->authLoaderMock);
    }

    public function testLoadPolicyLoadsData()
    {
        $modelStub = new \stdClass();

        $actualResult = $this->sut->savePolicy($modelStub);

        $this->assertTrue($actualResult);
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

        $modelStub = new \stdClass();
        $modelStub->model = ['g' => ['g' => $policyContainer]];

        $this->authLoaderMock
            ->expects($this->once())
            ->method('loadAll')
            ->willReturn($loadedData);

        $this->sut->loadPolicy($modelStub);

        $this->assertCount(2, $policyContainer->policy);
        $this->assertSame([$v00, $v01, '', '', ','], $policyContainer->policy[0]);
        $this->assertSame([$v10, $v11, '', '', ','], $policyContainer->policy[1]);
    }

    public function testSavePolicyReturnsTrue()
    {
        $modelStub = new \stdClass();

        $actualResult = $this->sut->savePolicy($modelStub);

        $this->assertTrue($actualResult);
    }

    public function testAddPolicyDoesNotThrowException()
    {
        $actualResult = $this->sut->addPolicy('foo', 'bar', []);

        $this->assertNull($actualResult);
    }

    public function testRemovePolicyReturnZero()
    {
        $actualResult = $this->sut->removePolicy('foo', 'bar', []);

        $this->assertSame(0, $actualResult);
    }

    public function testRemoveFilterPolicyThrowsCasbinException()
    {
        $this->expectException(CasbinException::class);

        $this->sut->removeFilteredPolicy('foo', 'bar', 'baz');
    }
}
