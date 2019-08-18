<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Admin\Orm\UserGroupRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\UserGroup as ValidatorFactory;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Cocur\Slugify\Slugify;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\IUnitOfWork;
use Opulence\Validation\IValidator;
use Opulence\Validation\Rules\Errors\ErrorCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserGroupTest extends TestCase
{
    /** @var UserGroup - System Under Test */
    protected $sut;

    /** @var GridRepo|MockObject */
    protected $gridRepoMock;

    /** @var ValidatorFactory|MockObject */
    protected $validatorFactoryMock;

    /** @var IUnitOfWork|MockObject */
    protected $unitOfWorkMock;

    /** @var IEventDispatcher|MockObject */
    protected $eventDispatcherMock;

    /** @var Slugify|MockObject */
    protected $slugifyMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->gridRepoMock = $this->getMockBuilder(GridRepo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add', 'delete', 'getById', 'getPage'])
            ->getMock();

        $this->validatorFactoryMock = $this->getMockBuilder(ValidatorFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createValidator'])
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

        $this->eventDispatcherMock = $this->getMockBuilder(IEventDispatcher::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['dispatch'])
            ->getMock();

        $this->slugifyMock = $this->getMockBuilder(Slugify::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->sut = new UserGroup(
            $this->gridRepoMock,
            $this->validatorFactoryMock,
            $this->unitOfWorkMock,
            $this->eventDispatcherMock,
            $this->slugifyMock
        );
    }

    public function testCreate()
    {
        $name       = 'Bar 123';
        $identifier = 'bar-123';
        $postData   = [
            'name' => $name,
        ];

        $this->gridRepoMock->expects($this->once())->method('add');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        /** @var IStringerEntity|Entity $actualResult */
        $actualResult = $this->sut->create($postData, []);

        $this->assertInstanceOf(Entity::class, $actualResult);
        $this->assertEmpty($actualResult->getId());
        $this->assertSame($identifier, $actualResult->getIdentifier());
        $this->assertSame($name, $actualResult->getName());
    }

    public function testCreateWithResources()
    {
        $name       = 'Bar 123';
        $identifier = 'bar-123';
        $postData   = [
            'name'               => $name,
            'admin_resource_ids' => ['ccbe55db-2f8d-4fd0-a74e-31fe8111ab3c', '5302a228-9d4a-4167-8c84-d60404d1247e'],
        ];

        $this->gridRepoMock->expects($this->once())->method('add');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        /** @var IStringerEntity|Entity $actualResult */
        $actualResult = $this->sut->create($postData, []);

        $this->assertInstanceOf(Entity::class, $actualResult);
        $this->assertEmpty($actualResult->getId());
        $this->assertSame($identifier, $actualResult->getIdentifier());
        $this->assertSame($name, $actualResult->getName());
        $this->assertCount(2, $actualResult->getAdminResources());
    }

    public function testUpdate()
    {
        $id     = 'foo';
        $entity = $this->sut->createEntity($id);

        $name       = 'Bar 123';
        $identifier = 'bar-123';
        $postData   = [
            'name' => $name,
        ];

        $this->gridRepoMock->expects($this->never())->method('add');
        $this->gridRepoMock->expects($this->never())->method('delete');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        $actualResult = $this->sut->update($entity, $postData, []);

        $this->assertTrue($actualResult);
        $this->assertSame($id, $entity->getId());
        $this->assertSame($identifier, $entity->getIdentifier());
        $this->assertSame($name, $entity->getName());
    }

    public function testUpdateThrowsExceptionWhenCalledWithWrongEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var IStringerEntity|MockObject $entityStub */
        $entityStub = $this->getMockBuilder(IStringerEntity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId', 'setId', 'toJSON', '__toString'])
            ->getMock();

        $this->sut->update($entityStub, [], []);
    }

    public function testDelete()
    {
        $id     = 'foo';
        $entity = $this->sut->createEntity($id);

        $this->gridRepoMock->expects($this->once())->method('delete');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        $actualResult = $this->sut->delete($entity);

        $this->assertTrue($actualResult);
    }

    public function testRetrieveEntity()
    {
        $id     = 'foo';
        $entity = $this->sut->createEntity($id);

        $this->gridRepoMock->expects($this->once())->method('getById')->willReturn($entity);

        $actualResult = $this->sut->retrieveEntity($id);

        $this->assertSame($entity, $actualResult);
    }

    public function testRetrieveList()
    {
        $offset     = 0;
        $limit      = 2;
        $orders     = [];
        $conditions = [];
        $params     = [];

        $id0            = 'foo';
        $entity0        = $this->sut->createEntity($id0);
        $id1            = 'bar';
        $entity1        = $this->sut->createEntity($id1);
        $expectedResult = [$entity0, $entity1];

        $this->gridRepoMock->expects($this->once())->method('getPage')->willReturn($expectedResult);

        $actualResult = $this->sut->retrieveList($offset, $limit, $orders, $conditions, $params);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testValidateFormSuccess()
    {
        $postData = ['foo' => 'bar'];

        $validatorMock = $this->getMockBuilder(IValidator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['field', 'isValid', 'getErrors'])
            ->getMock();
        $validatorMock->expects($this->once())->method('isValid')->with($postData)->willReturn(true);
        $validatorMock->expects($this->never())->method('getErrors');

        $this->validatorFactoryMock->expects($this->once())->method('createValidator')->willReturn($validatorMock);

        $result = $this->sut->validateForm($postData);

        $this->assertSame([], $result);
    }

    public function testValidateFormFailure()
    {
        $postData = ['foo' => 'bar'];

        $errorsStub        = new ErrorCollection();
        $errorsStub['foo'] = ['foo error'];

        $validatorMock = $this->getMockBuilder(IValidator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['field', 'isValid', 'getErrors'])
            ->getMock();
        $validatorMock->expects($this->once())->method('isValid')->with($postData)->willReturn(false);
        $validatorMock->expects($this->once())->method('getErrors')->willReturn($errorsStub);

        $this->validatorFactoryMock->expects($this->once())->method('createValidator')->willReturn($validatorMock);

        $result = $this->sut->validateForm($postData);

        $this->assertSame(['foo' => ['foo error']], $result);
    }

    public function testValidateCreatesOnlyOneValidator()
    {
        $postData = ['foo' => 'bar'];

        $validatorMock = $this->getMockBuilder(IValidator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['field', 'isValid', 'getErrors'])
            ->getMock();
        $validatorMock->expects($this->any())->method('isValid')->with($postData)->willReturn(true);
        $validatorMock->expects($this->any())->method('getErrors');

        $this->validatorFactoryMock->expects($this->once())->method('createValidator')->willReturn($validatorMock);

        $firstRun  = $this->sut->validateForm($postData);
        $secondRun = $this->sut->validateForm($postData);

        $this->assertSame($firstRun, $secondRun);
    }
}
