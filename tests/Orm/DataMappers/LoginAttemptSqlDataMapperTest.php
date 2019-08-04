<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\LoginAttempt;
use AbterPhp\Admin\Orm\DataMappers\LoginAttemptSqlDataMapper;
use AbterPhp\Framework\Orm\DataMappers\SqlTestCase;

class LoginAttemptSqlDataMapperTest extends SqlTestCase
{
    /** @var LoginAttemptSqlDataMapper */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new LoginAttemptSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);
    }

    public function testAdd()
    {
        $this->markTestIncomplete();
    }

    public function testDelete()
    {
        $this->markTestIncomplete();
    }

    public function testGetAll()
    {
        $this->markTestIncomplete();
    }

    public function testGetById()
    {
        $this->markTestIncomplete();
    }

    public function testUpdate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @param array        $expectedData
     * @param LoginAttempt $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
        $this->assertInstanceOf(LoginAttempt::class, $entity);
    }
}
