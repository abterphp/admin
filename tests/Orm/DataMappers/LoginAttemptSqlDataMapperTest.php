<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\LoginAttempt;
use AbterPhp\Admin\Orm\DataMappers\LoginAttemptSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\DataMapperTestCase;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use PHPUnit\Framework\MockObject\MockObject;

class LoginAttemptSqlDataMapperTest extends DataMapperTestCase
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
        $nextId    = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $ipHash    = 'foo';
        $username  = 'bar';
        $ipAddress = null;

        $sql       = 'INSERT INTO login_attempts (id, ip_hash, username, ip_address) VALUES (?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
            [$ipAddress, \PDO::PARAM_NULL],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new LoginAttempt($nextId, $ipHash, $username, $ipAddress);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testAddWithIp()
    {
        $nextId    = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $ipHash    = 'foo';
        $username  = 'bar';
        $ipAddress = '127.0.0.1';

        $sql       = 'INSERT INTO login_attempts (id, ip_hash, username, ip_address) VALUES (?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
            [$ipAddress, \PDO::PARAM_STR],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new LoginAttempt($nextId, $ipHash, $username, $ipAddress);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id        = '8fe2f659-dbe5-4995-9e07-f49fb018cfe7';
        $ipHash    = 'foo';
        $username  = 'bar';
        $ipAddress = '127.0.0.1';

        $sql       = 'DELETE FROM login_attempts AS login_attempts WHERE (id = ?)'; // phpcs:ignore
        $values    = [[$id, \PDO::PARAM_STR]];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new LoginAttempt($id, $ipHash, $username, $ipAddress);

        $this->sut->delete($entity);
    }

    public function testGetAll()
    {
        $id0        = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $ipHash0    = 'foo';
        $username0  = 'Foo';
        $id1        = '51eac0fc-2b26-4231-9559-469e59fae694';
        $ipHash1    = 'bar';
        $username1  = 'Bar';
        $ipAddress1 = '127.0.0.1';

        $sql          = 'SELECT login_attempts.id, login_attempts.ip_hash, login_attempts.username, login_attempts.ip_address FROM login_attempts'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            ['id' => $id0, 'ip_hash' => $ipHash0, 'username' => $username0, 'ip_address' => null],
            ['id' => $id1, 'ip_hash' => $ipHash1, 'username' => $username1, 'ip_address' => $ipAddress1],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetById()
    {
        $id       = '4b72daf8-81a9-400f-b865-28306d1c1646';
        $ipHash   = 'foo';
        $username = 'Foo';

        $sql          = 'SELECT login_attempts.id, login_attempts.ip_hash, login_attempts.username, login_attempts.ip_address FROM login_attempts WHERE (login_attempts.id = :login_attempt_id)'; // phpcs:ignore
        $values       = ['login_attempt_id' => [$id, \PDO::PARAM_STR]];
        $expectedData = [
            ['id' => $id, 'ip_hash' => $ipHash, 'username' => $username, 'ip_address' => null],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getById($id);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testUpdate()
    {
        $id       = '4b72daf8-81a9-400f-b865-28306d1c1646';
        $ipHash   = 'foo';
        $username = 'Foo';

        $sql       = 'UPDATE login_attempts AS login_attempts SET ip_hash = ?, username = ?, ip_address = ? WHERE (id = ?)'; // phpcs:ignore
        $values    = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
            [null, \PDO::PARAM_NULL],
            [$id, \PDO::PARAM_STR],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new LoginAttempt($id, $ipHash, $username);

        $this->sut->update($entity);
    }

    public function testAddThrowsExceptionIfCalledWithInvalidEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var IStringerEntity|MockObject $entity */
        $entity = $this->createMock(IStringerEntity::class);

        $this->sut->add($entity);
    }

    public function testDeleteThrowsExceptionIfCalledWithInvalidEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var IStringerEntity|MockObject $entity */
        $entity = $this->createMock(IStringerEntity::class);

        $this->sut->delete($entity);
    }

    public function testUpdateThrowsExceptionIfCalledWithInvalidEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        /** @var IStringerEntity|MockObject $entity */
        $entity = $this->createMock(IStringerEntity::class);

        $this->sut->update($entity);
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
