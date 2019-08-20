<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\LoginAttempt as Entity;
use AbterPhp\Admin\Orm\DataMappers\LoginAttemptSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\RepoTestCase;
use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IEntityRegistry;
use PHPUnit\Framework\MockObject\MockObject;

class LoginAttemptRepoTest extends RepoTestCase
{
    /** @var LoginAttemptRepo - System Under Test */
    protected $sut;

    /** @var LoginAttemptSqlDataMapper|MockObject */
    protected $dataMapperMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new LoginAttemptRepo($this->className, $this->dataMapperMock, $this->unitOfWorkMock);
    }

    /**
     * @return LoginAttemptSqlDataMapper|MockObject
     */
    protected function createDataMapperMock(): IDataMapper
    {
        /** @var LoginAttemptSqlDataMapper|MockObject $mock */
        $mock = $this->createMock(LoginAttemptSqlDataMapper::class);

        return $mock;
    }

    public function testGetAll()
    {
        $entityStub0 = new Entity('foo0', '127.0.0.1', 'foo-0');
        $entityStub1 = new Entity('foo1', '127.0.0.1', 'foo-1');
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getAll')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getAll();

        $this->assertSame($entities, $actualResult);
    }

    public function testGetByIdFromCache()
    {
        $entityStub = new Entity('foo0', '127.0.0.1', 'foo-0');

        $entityRegistry = $this->createEntityRegistryStub($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->never())->method('getById');

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByIdFromDataMapper()
    {
        $entityStub = new Entity('foo0', '127.0.0.1', 'foo-0');

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->once())->method('getById')->willReturn($entityStub);

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testAdd()
    {
        $entityStub = new Entity('foo0', '127.0.0.1', 'foo-0');

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForInsertion')->with($entityStub);

        $this->sut->add($entityStub);
    }

    public function testDelete()
    {
        $entityStub = new Entity('foo0', '127.0.0.1', 'foo-0');

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForDeletion')->with($entityStub);

        $this->sut->delete($entityStub);
    }

    /**
     * @param Entity|null $entity
     *
     * @return MockObject
     */
    protected function createEntityRegistryStub(?Entity $entity): MockObject
    {
        $entityRegistry = $this->createMock(IEntityRegistry::class);

        $entityRegistry->expects($this->any())->method('getEntity')->willReturn($entity);

        return $entityRegistry;
    }
}
