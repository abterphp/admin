<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Orm\DataMappers\AdminResourceSqlDataMapper;
use AbterPhp\Framework\Orm\DataMappers\SqlTestCase;

class AdminResourceSqlDataMapperTest extends SqlTestCase
{
    /** @var AdminResourceSqlDataMapper */
    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new AdminResourceSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);
    }

    public function testAdd()
    {
        $nextId     = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $identifier = 'foo';

        $sql    = 'INSERT INTO admin_resources (id, identifier) VALUES (?, ?)'; // phpcs:ignore
        $values = [[$nextId, \PDO::PARAM_STR], [$identifier, \PDO::PARAM_STR]];

        $this->prepare($this->writeConnectionMock, $sql, $this->createWriteStatement($values));
        $entity = new AdminResource($nextId, $identifier);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id         = '8fe2f659-dbe5-4995-9e07-f49fb018cfe7';
        $identifier = 'foo';

        $sql    = 'UPDATE admin_resources AS admin_resources SET deleted = ? WHERE (id = ?)'; // phpcs:ignore
        $values = [[1, \PDO::PARAM_INT], [$id, \PDO::PARAM_STR]];

        $this->prepare($this->writeConnectionMock, $sql, $this->createWriteStatement($values));
        $entity = new AdminResource($id, $identifier);

        $this->sut->delete($entity);
    }

    public function testGetAll()
    {
        $id         = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $identifier = 'foo';

        $sql          = 'SELECT ar.id, ar.identifier FROM admin_resources AS ar WHERE (ar.deleted = 0)'; // phpcs:ignore
        $values       = [];
        $expectedData = [['id' => $id, 'identifier' => $identifier]];

        $this->prepare($this->readConnectionMock, $sql, $this->createReadStatement($values, $expectedData));

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

        $this->prepare($this->readConnectionMock, $sql, $this->createReadStatement($values, $expectedData));

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

        $this->prepare($this->readConnectionMock, $sql, $this->createReadStatement($values, $expectedData));

        $actualResult = $this->sut->getByIdentifier($identifier);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testUpdate()
    {
        $id         = '91693481-276e-495b-82a1-33209c47ca09';
        $identifier = 'foo';

        $sql    = 'UPDATE admin_resources AS admin_resources SET identifier = ? WHERE (id = ?) AND (deleted = 0)'; // phpcs:ignore
        $values = [[$identifier, \PDO::PARAM_STR], [$id, \PDO::PARAM_STR]];

        $this->prepare($this->writeConnectionMock, $sql, $this->createWriteStatement($values));
        $entity = new AdminResource($id, $identifier);

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
