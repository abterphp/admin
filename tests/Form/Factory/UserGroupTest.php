<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Orm\IEntity;
use Opulence\Sessions\ISession;
use Opulence\Sessions\Session;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserGroupTest extends TestCase
{
    /** @var UserGroup - System Under Test */
    protected UserGroup $sut;

    /** @var ISession|MockObject */
    protected $sessionMock;

    /** @var ITranslator|MockObject */
    protected $translatorMock;

    /** @var AdminResourceRepo|MockObject */
    protected $adminResourceRepoMock;

    public function setUp(): void
    {
        $this->sessionMock = $this->createMock(Session::class);
        $this->sessionMock->expects($this->any())->method('get')->willReturnArgument(0);

        $this->translatorMock = $this->createMock(ITranslator::class);
        $this->translatorMock->expects($this->any())->method('translate')->willReturnArgument(0);

        $this->adminResourceRepoMock = $this->createMock(AdminResourceRepo::class);

        $this->sut = new UserGroup($this->sessionMock, $this->translatorMock, $this->adminResourceRepoMock);
    }

    public function testCreate()
    {
        $action            = 'foo';
        $method            = RequestMethods::POST;
        $showUrl           = 'bar';
        $entityId          = '26f69be3-fa57-4ad1-8c58-5f4631040ece';
        $identifier        = 'blah';
        $name              = 'zorros';
        $allAdminResources = [
            new AdminResource('8a42e773-975d-41bd-9061-57ee6c381e68', 'ar-21'),
            new AdminResource('5180d59e-3b79-4c3b-8877-7df8086a8879', 'ar-47'),
            new AdminResource('08e82847-9342-42ae-8563-ef2bae335c7a', 'ar-64'),
            new AdminResource('c2d3f41c-15ba-4664-8393-8024bf650d21', 'ar-187'),
        ];
        $adminResources    = [$allAdminResources[1], $allAdminResources[3]];

        $this->adminResourceRepoMock
            ->expects($this->any())
            ->method('getAll')
            ->willReturn($allAdminResources);

        $entityStub = new Entity($entityId, $identifier, $name, $adminResources);

        $form = (string)$this->sut->create($action, $method, $showUrl, $entityStub);

        $this->assertStringContainsString($action, $form);
        $this->assertStringContainsString($showUrl, $form);
        $this->assertStringContainsString('CSRF', $form);
        $this->assertStringContainsString('POST', $form);
        $this->assertStringContainsString('identifier', $form);
        $this->assertStringContainsString('name', $form);
        $this->assertStringContainsString('admin_resource_ids', $form);
        $this->assertStringContainsString('selected', $form);
        $this->assertStringContainsString('button', $form);
    }

    public function testCreateThrowsExceptionIfWrongEntityIsProvided()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entityStub = $this->createMock(IEntity::class);

        $this->sut->create('foo', 'bar', '/baz', $entityStub);
    }

    /**
     * @return MockObject|Entity
     */
    protected function createMockEntity()
    {
        $entityMock = $this->createMock(Entity::class);

        return $entityMock;
    }
}
