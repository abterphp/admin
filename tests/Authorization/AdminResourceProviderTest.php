<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\AdminResourceAuthLoader;
use Casbin\Exceptions\CasbinException;
use Casbin\Model\Model;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminResourceProviderTest extends TestCase
{
    /** @var AdminResourceProvider - System Under Test */
    protected AdminResourceProvider $sut;

    /** @var MockObject|AdminResourceAuthLoader */
    protected $authLoaderMock;

    public function setUp(): void
    {
        $this->authLoaderMock = $this->createMock(AdminResourceAuthLoader::class);

        $this->sut = new AdminResourceProvider($this->authLoaderMock);
    }

    public function testLoadPolicyLoadsData()
    {
        $modelMock = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->authLoaderMock->expects($this->once())->method('loadAll')->willReturn([]);

        $this->sut->loadPolicy($modelMock);
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
        $modelMock->expects($this->exactly(4))->method('addPolicy');

        $this->authLoaderMock
            ->expects($this->once())
            ->method('loadAll')
            ->willReturn($loadedData);

        $this->sut->loadPolicy($modelMock);
    }

    public function testSavePolicyReturnsTrue()
    {
        $this->expectException(CasbinException::class);

        $modelMock = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->savePolicy($modelMock);
    }

    public function testAddPolicyDoesNotThrowException()
    {
        $this->expectException(CasbinException::class);

        $this->sut->addPolicy('foo', 'bar', []);
    }

    public function testRemovePolicyReturnZero()
    {
        $this->expectException(CasbinException::class);

        $this->sut->removePolicy('foo', 'bar', []);
    }

    public function testRemoveFilterPolicyThrowsCasbinException()
    {
        $this->expectException(CasbinException::class);

        $this->sut->removeFilteredPolicy('foo', 'bar', 0);
    }
}
