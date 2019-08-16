<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\AdminResource as Entity;
use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IEntityRegistry;
use Opulence\Orm\IUnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdminResourceRepoTest extends TestCase
{
    /** @var AdminResourceRepo - System Under Test */
    protected $sut;

    /** @var string */
    protected $className = 'Foo';

    /** @var IDataMapper|MockObject */
    protected $dataMapperMock;

    /** @var IUnitOfWork|MockObject */
    protected $unitOfWorkMock;

    public function setUp(): void
    {
        $this->dataMapperMock = $this->getMockBuilder(IDataMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['add', 'delete', 'getAll', 'getById', 'update', 'getByIdentifier', 'getByUserId'])
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

        $this->sut = new AdminResourceRepo($this->className, $this->dataMapperMock, $this->unitOfWorkMock);
    }

    public function testGetAll()
    {
        $entityStub0 = new Entity('foo0', 'foo-0');
        $entityStub1 = new Entity('foo1', 'foo-1');
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getAll')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getAll();

        $this->assertSame($entities, $actualResult);
    }

    public function testGetByIdFromCache()
    {
        $entityStub = new Entity('foo0', 'foo-0');

        $entityRegistry = $this->createEntityRegistryStub($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->never())->method('getById');

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByIdFromDataMapper()
    {
        $entityStub = new Entity('foo0', 'foo-0');

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->once())->method('getById')->willReturn($entityStub);

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testAdd()
    {
        $entityStub = new Entity('foo0', 'foo-0');

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForInsertion')->with($entityStub);

        $this->sut->add($entityStub);
    }

    public function testDelete()
    {
        $entityStub = new Entity('foo0', 'foo-0');

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForDeletion')->with($entityStub);

        $this->sut->delete($entityStub);
    }

    public function testGetByIdentifier()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByIdentifier')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByIdentifier($identifier);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByUserId()
    {
        $userId = 'user0';

        $entityStub0 = new Entity('foo0', 'foo-0');
        $entityStub1 = new Entity('foo1', 'foo-1');
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByUserId')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByUserId($userId);

        $this->assertSame($entities, $actualResult);
    }

    /**
     * @param Entity|null $entity
     *
     * @return MockObject
     */
    protected function createEntityRegistryStub(?Entity $entity): MockObject
    {
        $entityRegistry = $this->getMockBuilder(IEntityRegistry::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'clear',
                'clearAggregateRoots',
                'deregisterEntity',
                'getClassName',
                'getEntities',
                'getEntity',
                'getEntityState',
                'getObjectHashId',
                'isRegistered',
                'registerAggregateRootCallback',
                'registerEntity',
                'runAggregateRootCallbacks',
                'setState',
            ])
            ->getMock();

        $entityRegistry->expects($this->any())->method('registerEntity');
        $entityRegistry->expects($this->any())->method('getEntity')->willReturn($entity);

        return $entityRegistry;
    }
}
