<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\Token as Entity;
use AbterPhp\Admin\Orm\DataMappers\TokenSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\RepoTestCase;
use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IEntityRegistry;
use PHPUnit\Framework\MockObject\MockObject;

class TokenRepoTest extends RepoTestCase
{
    /** @var TokenRepo - System Under Test */
    protected $sut;

    /** @var TokenSqlDataMapper|MockObject */
    protected $dataMapperMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new TokenRepo($this->className, $this->dataMapperMock, $this->unitOfWorkMock);
    }

    /**
     * @return TokenSqlDataMapper|MockObject
     */
    protected function createDataMapperMock(): IDataMapper
    {
        /** @var TokenSqlDataMapper|MockObject $mock */
        $mock = $this->createMock(TokenSqlDataMapper::class);

        return $mock;
    }

    public function testGetAll()
    {
        $entityStub0 = new Entity('foo0', 'foo-0', new \DateTimeImmutable(), null);
        $entityStub1 = new Entity('foo1', 'foo-1', new \DateTimeImmutable(), new \DateTimeImmutable());
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getAll')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getAll();

        $this->assertSame($entities, $actualResult);
    }

    public function testGetByIdFromCache()
    {
        $entityStub = new Entity('foo0', 'foo-0', new \DateTimeImmutable(), null);

        $entityRegistry = $this->createEntityRegistryStub($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->never())->method('getById');

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByIdFromDataMapper()
    {
        $entityStub = new Entity('foo0', 'foo-0', new \DateTimeImmutable(), null);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->once())->method('getById')->willReturn($entityStub);

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testAdd()
    {
        $entityStub = new Entity('foo0', 'foo-0', new \DateTimeImmutable(), null);

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForInsertion')->with($entityStub);

        $this->sut->add($entityStub);
    }

    public function testAddWithRevokedAt()
    {
        $entityStub = new Entity('foo0', 'foo-0', new \DateTimeImmutable(), new \DateTimeImmutable());

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForInsertion')->with($entityStub);

        $this->sut->add($entityStub);
    }

    public function testDelete()
    {
        $entityStub = new Entity('foo0', 'foo-0', new \DateTimeImmutable(), null);

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForDeletion')->with($entityStub);

        $this->sut->delete($entityStub);
    }

    public function testGetByClientId()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier, new \DateTimeImmutable(), null);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByClientId')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByClientId($identifier);

        $this->assertSame($entityStub, $actualResult);
    }

    /**
     * @param Entity|null $entity
     *
     * @return MockObject
     */
    protected function createEntityRegistryStub(?Entity $entity): MockObject
    {
        $entityRegistry = $this->createMock(IEntityRegistry::class);

        $entityRegistry->expects($this->any())->method('registerEntity');
        $entityRegistry->expects($this->any())->method('getEntity')->willReturn($entity);

        return $entityRegistry;
    }
}
