<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\DataMappers\UserSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\RepoTestCase;
use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IEntityRegistry;
use Opulence\Orm\IUnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;

class UserRepoTest extends RepoTestCase
{
    /** @var UserRepo - System Under Test */
    protected $sut;

    /** @var string */
    protected $className = 'Foo';

    /** @var IDataMapper|MockObject */
    protected $dataMapperMock;

    /** @var IUnitOfWork|MockObject */
    protected $unitOfWorkMock;

    /** @var UserLanguage */
    protected $userLanguageStub;

    public function setUp(): void
    {
        parent::setUp();

        $this->userLanguageStub = new UserLanguage('', '', '');

        $this->sut = new UserRepo($this->className, $this->dataMapperMock, $this->unitOfWorkMock);
    }

    /**
     * @return UserSqlDataMapper|MockObject
     */
    protected function createDataMapperMock(): IDataMapper
    {
        /** @var UserSqlDataMapper|MockObject $mock */
        $mock = $this->getMockBuilder(UserSqlDataMapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    public function testGetAll()
    {
        $entityStub0 = new Entity('foo0', 'foo-0', '', '', false, false, $this->userLanguageStub);
        $entityStub1 = new Entity('foo1', 'foo-1', '', '', false, false, $this->userLanguageStub);
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getAll')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getAll();

        $this->assertSame($entities, $actualResult);
    }

    public function testGetByIdFromCache()
    {
        $entityStub = new Entity('foo0', 'foo-0', '', '', false, false, $this->userLanguageStub);

        $entityRegistry = $this->createEntityRegistryStub($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->never())->method('getById');

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByIdFromDataMapper()
    {
        $entityStub = new Entity('foo0', 'foo-0', '', '', false, false, $this->userLanguageStub);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $this->dataMapperMock->expects($this->once())->method('getById')->willReturn($entityStub);

        $id = 'foo';

        $actualResult = $this->sut->getById($id);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testAdd()
    {
        $entityStub = new Entity('foo0', 'foo-0', '', '', false, false, $this->userLanguageStub);

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForInsertion')->with($entityStub);

        $this->sut->add($entityStub);
    }

    public function testDelete()
    {
        $entityStub = new Entity('foo0', 'foo-0', '', '', false, false, $this->userLanguageStub);

        $this->unitOfWorkMock->expects($this->once())->method('scheduleForDeletion')->with($entityStub);

        $this->sut->delete($entityStub);
    }

    public function testGetPage()
    {
        $entityStub0 = new Entity('foo0', 'foo-0', '', '', false, false, $this->userLanguageStub);
        $entityStub1 = new Entity('foo1', 'foo-1', '', '', false, false, $this->userLanguageStub);
        $entities    = [$entityStub0, $entityStub1];

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getPage')->willReturn($entities);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getPage(0, 10, [], [], []);

        $this->assertSame($entities, $actualResult);
    }

    public function testGetByClientId()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier, '', '', false, false, $this->userLanguageStub);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByClientId')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByClientId($identifier);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByUsername()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier, '', '', false, false, $this->userLanguageStub);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByUsername')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByUsername($identifier);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testGetByEmail()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier, '', '', false, false, $this->userLanguageStub);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('getByEmail')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->getByEmail($identifier);

        $this->assertSame($entityStub, $actualResult);
    }

    public function testFind()
    {
        $identifier = 'foo-0';
        $entityStub = new Entity('foo0', $identifier, '', '', false, false, $this->userLanguageStub);

        $entityRegistry = $this->createEntityRegistryStub(null);

        $this->dataMapperMock->expects($this->once())->method('find')->willReturn($entityStub);

        $this->unitOfWorkMock->expects($this->any())->method('getEntityRegistry')->willReturn($entityRegistry);

        $actualResult = $this->sut->find($identifier);

        $this->assertSame($entityStub, $actualResult);
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
