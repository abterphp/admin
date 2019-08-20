<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\AdminResourceAuthLoader;
use Casbin\Exceptions\CasbinException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminResourceProviderTest extends TestCase
{
    /** @var AdminResourceProvider - System Under Test */
    protected $sut;

    /** @var MockObject|AdminResourceAuthLoader */
    protected $authLoaderMock;

    public function setUp(): void
    {
        $this->authLoaderMock = $this->createMock(AdminResourceAuthLoader::class);

        $this->sut = new AdminResourceProvider($this->authLoaderMock);
    }

    public function testLoadPolicyLoadsData()
    {
        $modelStub = new \stdClass();

        $this->authLoaderMock->expects($this->once())->method('loadAll')->willReturn([]);

        $this->sut->loadPolicy($modelStub);
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
        $modelStub->model = ['p' => ['p' => $policyContainer]];

        $this->authLoaderMock
            ->expects($this->once())
            ->method('loadAll')
            ->willReturn($loadedData);

        $this->sut->loadPolicy($modelStub);

        $r01 = sprintf('admin_resource_%s', $v01);
        $r11 = sprintf('admin_resource_%s', $v11);

        $this->assertCount(4, $policyContainer->policy);
        $this->assertSame([$v00, $r01, 'read', '', ','], $policyContainer->policy[0]);
        $this->assertSame([$v00, $r01, 'write', '', ','], $policyContainer->policy[1]);
        $this->assertSame([$v10, $r11, 'read', '', ','], $policyContainer->policy[2]);
        $this->assertSame([$v10, $r11, 'write', '', ','], $policyContainer->policy[3]);
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
