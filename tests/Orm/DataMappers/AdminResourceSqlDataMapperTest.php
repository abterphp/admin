<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Orm\DataMappers\AdminResourceSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\DataMapperTestCase;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;

class AdminResourceSqlDataMapperTest extends DataMapperTestCase
{
    /** @var AdminResourceSqlDataMapper */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new AdminResourceSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);
    }

    public function testAdd()
    {
        $nextId     = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $identifier = 'foo';

        $sql       = 'INSERT INTO admin_resources (id, identifier) VALUES (?, ?)'; // phpcs:ignore
        $values    = [[$nextId, \PDO::PARAM_STR], [$identifier, \PDO::PARAM_STR]];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new AdminResource($nextId, $identifier);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id         = '8fe2f659-dbe5-4995-9e07-f49fb018cfe7';
        $identifier = 'foo';

        $sql       = 'UPDATE admin_resources AS admin_resources SET deleted = ? WHERE (id = ?)'; // phpcs:ignore
        $values    = [[1, \PDO::PARAM_INT], [$id, \PDO::PARAM_STR]];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new AdminResource($id, $identifier);

        $this->sut->delete($entity);
    }

    public function testGetAll()
    {
        $id0         = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $identifier0 = 'foo';
        $id1         = '51eac0fc-2b26-4231-9559-469e59fae694';
        $identifier1 = 'bar';

        $sql          = 'SELECT ar.id, ar.identifier FROM admin_resources AS ar WHERE (ar.deleted = 0)'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            ['id' => $id0, 'identifier' => $identifier0],
            ['id' => $id1, 'identifier' => $identifier1],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetById()
    {
        $id         = '4b72daf8-81a9-400f-b865-28306d1c1646';
        $identifier = 'foo';

        $sql          = 'SELECT ar.id, ar.identifier FROM admin_resources AS ar WHERE (ar.deleted = 0) AND (ar.id = :admin_resource_id)'; // phpcs:ignore
        $values       = ['admin_resource_id' => [$id, \PDO::PARAM_STR]];
        $expectedData = [['id' => $id, 'identifier' => $identifier]];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getById($id);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testGetByIdentifier()
    {
        $id         = '998ac138-85be-4b8f-ac7a-3fb8c249a7bf';
        $identifier = 'foo';

        $sql          = 'SELECT ar.id, ar.identifier FROM admin_resources AS ar WHERE (ar.deleted = 0) AND (ar.identifier = :identifier)'; // phpcs:ignore
        $values       = ['identifier' => [$identifier, \PDO::PARAM_STR]];
        $expectedData = [['id' => $id, 'identifier' => $identifier]];
        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getByIdentifier($identifier);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testGetByUserId()
    {
        $userId      = '81704325-5d93-4987-8425-8a80062db406';
        $id0         = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $identifier0 = 'foo';
        $id1         = '51eac0fc-2b26-4231-9559-469e59fae694';
        $identifier1 = 'bar';

        $sql          = 'SELECT ar.id, ar.identifier FROM admin_resources AS ar INNER JOIN user_groups_admin_resources AS ugar ON ugar.admin_resource_id = ar.id INNER JOIN user_groups AS ug ON ug.id = ugar.user_group_id INNER JOIN users_user_groups AS uug ON uug.user_group_id = ug.id WHERE (ar.deleted = 0) AND (uug.user_id = :user_id) GROUP BY ar.id'; // phpcs:ignore
        $values       = ['user_id' => [$userId, \PDO::PARAM_STR]];
        $expectedData = [
            ['id' => $id0, 'identifier' => $identifier0],
            ['id' => $id1, 'identifier' => $identifier1],
        ];
        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getByUserId($userId);

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testUpdate()
    {
        $id         = '91693481-276e-495b-82a1-33209c47ca09';
        $identifier = 'foo';

        $sql       = 'UPDATE admin_resources AS admin_resources SET identifier = ? WHERE (id = ?) AND (deleted = 0)'; // phpcs:ignore
        $values    = [[$identifier, \PDO::PARAM_STR], [$id, \PDO::PARAM_STR]];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new AdminResource($id, $identifier);

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
     * @param array         $expectedData
     * @param AdminResource $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
        $this->assertInstanceOf(AdminResource::class, $entity);
        $this->assertEquals($expectedData['id'], $entity->getId());
        $this->assertSame($expectedData['identifier'], $entity->getIdentifier());
    }
}
