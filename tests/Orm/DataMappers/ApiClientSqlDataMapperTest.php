<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\ApiClient;
use AbterPhp\Admin\Orm\DataMappers\ApiClientSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\DataMapperTestCase;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;

class ApiClientSqlDataMapperTest extends DataMapperTestCase
{
    /** @var ApiClientSqlDataMapper */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new ApiClientSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);
    }

    public function testAdd()
    {
        $nextId      = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'bar';

        $sql       = 'INSERT INTO api_clients (id, user_id, description, secret) VALUES (?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$userId, \PDO::PARAM_STR],
            [$description, \PDO::PARAM_STR],
            [$secret, \PDO::PARAM_STR],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new ApiClient($nextId, $userId, $description, $secret);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id          = '8fe2f659-dbe5-4995-9e07-f49fb018cfe7';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'bar';

        $sql0       = 'DELETE FROM api_clients_admin_resources WHERE (api_client_id = ?)'; // phpcs:ignore
        $values0    = [[$id, \PDO::PARAM_STR]];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1       = 'UPDATE api_clients AS api_clients SET deleted = ? WHERE (id = ?)'; // phpcs:ignore
        $values1    = [[1, \PDO::PARAM_INT], [$id, \PDO::PARAM_STR]];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);
        $entity = new ApiClient($id, $userId, $description, $secret);

        $this->sut->delete($entity);
    }

    public function testGetAll()
    {
        $id0          = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $userId0      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description0 = 'foo';
        $secret0      = 'foo-secret';
        $id1          = '51eac0fc-2b26-4231-9559-469e59fae694';
        $userId1      = '27c3e7b4-d88b-4e7a-9f87-d48729842cd7';
        $description1 = 'bar';
        $secret1      = 'bar-secret';

        $sql          = 'SELECT ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted = 0) GROUP BY ac.id'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            ['id' => $id0, 'user_id' => $userId0, 'description' => $description0, 'secret' => $secret0],
            ['id' => $id1, 'user_id' => $userId1, 'description' => $description1, 'secret' => $secret1],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetById()
    {
        $id          = '4b72daf8-81a9-400f-b865-28306d1c1646';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'foo-secret';

        $sql          = 'SELECT ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted = 0) AND (ac.id = :api_client_id) GROUP BY ac.id'; // phpcs:ignore
        $values       = ['api_client_id' => [$id, \PDO::PARAM_STR]];
        $expectedData = [['id' => $id, 'user_id' => $userId, 'description' => $description, 'secret' => $secret]];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getById($id);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testUpdate()
    {
        $id          = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'bar';

        $sql0       = 'UPDATE api_clients AS api_clients SET description = ?, secret = ? WHERE (id = ?) AND (deleted = 0)'; // phpcs:ignore
        $values0    = [
            [$description, \PDO::PARAM_STR],
            [$secret, \PDO::PARAM_STR],
            [$id, \PDO::PARAM_STR],
        ];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1       = 'DELETE FROM api_clients_admin_resources WHERE (api_client_id = ?)'; // phpcs:ignore
        $values1    = [[$id, \PDO::PARAM_STR]];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $entity = new ApiClient($id, $userId, $description, $secret);
        $this->sut->update($entity);
    }

    public function testAddThrowsExceptionIfCalledWithInvalidEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entity = $this->getMockBuilder(IStringerEntity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString', 'toJSON', 'getId', 'setId'])
            ->getMock();

        $this->sut->add($entity);
    }

    public function testDeleteThrowsExceptionIfCalledWithInvalidEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entity = $this->getMockBuilder(IStringerEntity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString', 'toJSON', 'getId', 'setId'])
            ->getMock();

        $this->sut->delete($entity);
    }

    public function testUpdateThrowsExceptionIfCalledWithInvalidEntity()
    {
        $this->expectException(\InvalidArgumentException::class);

        $entity = $this->getMockBuilder(IStringerEntity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString', 'toJSON', 'getId', 'setId'])
            ->getMock();

        $this->sut->update($entity);
    }

    /**
     * @param array     $expectedData
     * @param ApiClient $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
        $this->assertInstanceOf(ApiClient::class, $entity);
        $this->assertEquals($expectedData['id'], $entity->getId());
        $this->assertSame($expectedData['user_id'], $entity->getUserId());
        $this->assertSame($expectedData['description'], $entity->getDescription());
        $this->assertSame($expectedData['secret'], $entity->getSecret());
    }
}
