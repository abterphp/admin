<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Orm\UserRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\User as ValidatorFactory;
use AbterPhp\Framework\Crypto\Crypto;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\IUnitOfWork;
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
            ->onlyMethods([])
            ->getMock();

        $this->validatorFactoryMock = $this->getMockBuilder(ValidatorFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->unitOfWorkMock = $this->getMockBuilder(IUnitOfWork::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->eventDispatcherMock = $this->getMockBuilder(IEventDispatcher::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->cryptoMock = $this->getMockBuilder(Crypto::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->sut = new User(
            $this->gridRepoMock,
            $this->validatorFactoryMock,
            $this->unitOfWorkMock,
            $this->eventDispatcherMock,
            $this->cryptoMock
        );
    }

    public function testIncomplete()
    {
        $this->markTestIncomplete();
    }
}
