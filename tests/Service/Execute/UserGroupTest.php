<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Orm\UserGroupRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\UserGroup as ValidatorFactory;
use Cocur\Slugify\Slugify;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\IUnitOfWork;
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

    public function testIncomplete()
    {
        $this->markTestIncomplete();
    }
}
