<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Orm\UserRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\User as ValidatorFactory;
use AbterPhp\Framework\Crypto\Crypto;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\IUnitOfWork;
use Opulence\Validation\IValidator;
use Opulence\Validation\Rules\Errors\ErrorCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @var User - System Under Test */
    protected $sut;

    /** @var GridRepo|MockObject */
    protected $gridRepoMock;

    /** @var ValidatorFactory|MockObject */
    protected $validatorFactoryMock;

    /** @var IUnitOfWork|MockObject */
    protected $unitOfWorkMock;

    /** @var IEventDispatcher|MockObject */
    protected $eventDispatcherMock;

    /** @var Crypto|MockObject */
    protected $cryptoMock;

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

        $this->cryptoMock = $this->getMockBuilder(Crypto::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['prepareSecret', 'hashCrypt'])
            ->getMock();

        $this->sut = new User(
            $this->gridRepoMock,
            $this->validatorFactoryMock,
            $this->unitOfWorkMock,
            $this->eventDispatcherMock,
            $this->cryptoMock
        );
    }

    public function testCreateMinimal()
    {
        $postData = [];

        $this->gridRepoMock->expects($this->once())->method('add');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        /** @var IStringerEntity|Entity $actualResult */
        $actualResult = $this->sut->create($postData, []);

        $this->assertInstanceOf(Entity::class, $actualResult);
        $this->assertEmpty($actualResult->getId());
        $this->assertSame('', $actualResult->getPassword());
        $this->assertFalse($actualResult->canLogin());
        $this->assertFalse($actualResult->isGravatarAllowed());
        $this->assertSame('', $actualResult->getUserLanguage()->getId());
        $this->assertCount(0, $actualResult->getUserGroups());
    }

    public function testCreate()
    {
        $username       = 'Foo';
        $email          = 'foor@example.com';
        $userLanguageId = 'ul-1';
        $userGroupIds   = ['ug-1', 'ug-2'];
        $postData       = [
            'username'            => $username,
            'email'               => $email,
            'is_gravatar_allowed' => true,
            'can_login'           => true,
            'user_language_id'    => $userLanguageId,
            'user_group_ids'      => $userGroupIds,
        ];

        $this->gridRepoMock->expects($this->once())->method('add');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        /** @var IStringerEntity|Entity $actualResult */
        $actualResult = $this->sut->create($postData, []);

        $this->assertInstanceOf(Entity::class, $actualResult);
        $this->assertEmpty($actualResult->getId());
        $this->assertTrue($actualResult->canLogin());
        $this->assertTrue($actualResult->isGravatarAllowed());
        $this->assertSame($userLanguageId, $actualResult->getUserLanguage()->getId());
        $this->assertCount(2, $actualResult->getUserGroups());
    }

    public function testCreateWithPassword()
    {
        $password       = 'foo';
        $hashedPassword = 'foo-hashed';
        $postData       = [
            'password' => $password,
        ];

        $this->gridRepoMock->expects($this->once())->method('add');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');
        $this->cryptoMock->expects($this->once())->method('hashCrypt')->with($password)->willReturn($hashedPassword);

        /** @var IStringerEntity|Entity $actualResult */
        $actualResult = $this->sut->create($postData, []);

        $this->assertInstanceOf(Entity::class, $actualResult);
        $this->assertEmpty($actualResult->getId());
        $this->assertSame($hashedPassword, $actualResult->getPassword());
    }

    public function testUpdate()
    {
        $id     = 'foo';
        $entity = $this->sut->createEntity($id);

        $username       = 'Foo';
        $email          = 'foor@example.com';
        $userLanguageId = 'ul-1';
        $userGroupIds   = ['ug-1', 'ug-2'];
        $postData       = [
            'username'            => $username,
            'email'               => $email,
            'is_gravatar_allowed' => true,
            'can_login'           => true,
            'user_language_id'    => $userLanguageId,
            'user_group_ids'      => $userGroupIds,
        ];

        $this->gridRepoMock->expects($this->never())->method('add');
        $this->gridRepoMock->expects($this->never())->method('delete');
        $this->eventDispatcherMock->expects($this->atLeastOnce())->method('dispatch');
        $this->unitOfWorkMock->expects($this->once())->method('commit');

        $actualResult = $this->sut->update($entity, $postData, []);

        $this->assertTrue($actualResult);
        $this->assertSame($id, $entity->getId());
        $this->assertTrue($entity->canLogin());
        $this->assertTrue($entity->isGravatarAllowed());
        $this->assertSame($userLanguageId, $entity->getUserLanguage()->getId());
        $this->assertCount(2, $entity->getUserGroups());
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
}
