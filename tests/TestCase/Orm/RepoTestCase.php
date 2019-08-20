<?php

declare(strict_types=1);

namespace AbterPhp\Admin\TestCase\Orm;

use Opulence\Orm\DataMappers\IDataMapper;
use Opulence\Orm\IUnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class RepoTestCase extends TestCase
{
    /** @var string */
    protected $className = 'Foo';

    /** @var IDataMapper|MockObject */
    protected $dataMapperMock;

    /** @var IUnitOfWork|MockObject */
    protected $unitOfWorkMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->dataMapperMock = $this->createDataMapperMock();

        $this->unitOfWorkMock = $this->createUnitOfWorkMock();
    }

    /**
     * @return IDataMapper|MockObject
     */
    protected function createDataMapperMock(): IDataMapper
    {
        /** @var IDataMapper|MockObject $mock */
        $mock = $this->getMockBuilder(IDataMapper::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    /**
     * @return IUnitOfWork|MockObject
     */
    protected function createUnitOfWorkMock(): IUnitOfWork
    {
        /** @var IUnitOfWork|MockObject $mock */
        $mock = $this->getMockBuilder(IUnitOfWork::class)
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

        return $mock;
    }
}
