<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\ApiClient;
use AbterPhp\Admin\Orm\DataMappers\ApiClientSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\DataMapperTestCase;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use Opulence\Orm\Ids\Generators\IIdGenerator;
use PHPUnit\Framework\MockObject\MockObject;

class ApiClientSqlDataMapperTest extends DataMapperTestCase
{
    /** @var ApiClientSqlDataMapper */
    protected $sut;

    /** @var MockObject|IIdGenerator */
    protected $idGeneratorMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->idGeneratorMock = $this->createMock(IIdGenerator::class);

        $this->sut = new ApiClientSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);

        $this->sut->setIdGenerator($this->idGeneratorMock);
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

    public function testAddWithAdminResources()
    {
        $nextId      = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'bar';

        $acarId0     = '4c3c3152-fc34-403e-a83e-7d7ce12b8cc4';
        $resourceId0 = 'e5f9d4b5-92c7-4dac-9feb-c297a7127878';

        $acarId1     = '8060491b-909f-4154-8886-62f06267c864';
        $resourceId1 = '5a2feca1-42dd-4bc4-81b3-9ff55da83e95';

        $this->idGeneratorMock->expects($this->at(0))->method('generate')->willReturn($acarId0);
        $this->idGeneratorMock->expects($this->at(1))->method('generate')->willReturn($acarId1);

        $sql0       = 'INSERT INTO api_clients (id, user_id, description, secret) VALUES (?, ?, ?, ?)'; // phpcs:ignore
        $values0    = [
            [$nextId, \PDO::PARAM_STR],
            [$userId, \PDO::PARAM_STR],
            [$description, \PDO::PARAM_STR],
            [$secret, \PDO::PARAM_STR],
        ];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $resource0 = new AdminResource($resourceId0, '');
        $resource1 = new AdminResource($resourceId1, '');
        $entity    = new ApiClient($nextId, $userId, $description, $secret, [$resource0, $resource1]);

        $sql1       = 'INSERT INTO api_clients_admin_resources (id, api_client_id, admin_resource_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values1    = [
            [$acarId0, \PDO::PARAM_STR],
            [$nextId, \PDO::PARAM_STR],
            [$resourceId0, \PDO::PARAM_STR],
        ];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $sql1       = 'INSERT INTO api_clients_admin_resources (id, api_client_id, admin_resource_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values1    = [
            [$acarId1, \PDO::PARAM_STR],
            [$nextId, \PDO::PARAM_STR],
            [$resourceId1, \PDO::PARAM_STR],
        ];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 2);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id          = '8fe2f659-dbe5-4995-9e07-f49fb018cfe7';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'bar';

        $resourceId0 = 'e5f9d4b5-92c7-4dac-9feb-c297a7127878';

        $resourceId1 = '5a2feca1-42dd-4bc4-81b3-9ff55da83e95';

        $sql0       = 'DELETE FROM api_clients_admin_resources WHERE (api_client_id = ?)'; // phpcs:ignore
        $values0    = [[$id, \PDO::PARAM_STR]];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1       = 'UPDATE api_clients AS api_clients SET deleted_at = NOW() WHERE (id = ?)'; // phpcs:ignore
        $values1    = [[$id, \PDO::PARAM_STR]];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $resource0 = new AdminResource($resourceId0, '');
        $resource1 = new AdminResource($resourceId1, '');
        $entity    = new ApiClient($id, $userId, $description, $secret, [$resource0, $resource1]);

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

        $sql          = 'SELECT ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted_at IS NULL) GROUP BY ac.id'; // phpcs:ignore
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

    public function testGetAllWithAdminResources()
    {
        $id0            = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $userId0        = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description0   = 'foo';
        $secret0        = 'foo-secret';
        $arId00         = 'da9716ee-c5c1-4ea8-a762-48eb44f1df45';
        $arIdentifier00 = 'foo-ar0';
        $arId01         = 'd2d3c06c-8371-41f7-b6e0-75d8d4ac054e';
        $arIdentifier01 = 'foo-ar1';
        $id1            = '51eac0fc-2b26-4231-9559-469e59fae694';
        $userId1        = '27c3e7b4-d88b-4e7a-9f87-d48729842cd7';
        $description1   = 'bar';
        $secret1        = 'bar-secret';
        $arId10         = '';
        $arIdentifier10 = '';

        $sql          = 'SELECT ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted_at IS NULL) GROUP BY ac.id'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            [
                'id'                         => $id0,
                'user_id'                    => $userId0,
                'description'                => $description0,
                'secret'                     => $secret0,
                'admin_resource_ids'         => "$arId00,$arId01",
                'admin_resource_identifiers' => "$arIdentifier00,$arIdentifier01",
            ],
            [
                'id'                         => $id1,
                'user_id'                    => $userId1,
                'description'                => $description1,
                'secret'                     => $secret1,
                'admin_resource_ids'         => $arId10,
                'admin_resource_identifiers' => $arIdentifier10,
            ],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetPage()
    {
        $id0          = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $userId0      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description0 = 'foo';
        $secret0      = 'foo-secret';
        $id1          = '51eac0fc-2b26-4231-9559-469e59fae694';
        $userId1      = '27c3e7b4-d88b-4e7a-9f87-d48729842cd7';
        $description1 = 'bar';
        $secret1      = 'bar-secret';

        $sql          = 'SELECT SQL_CALC_FOUND_ROWS ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted_at IS NULL) GROUP BY ac.id LIMIT 10 OFFSET 0'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            ['id' => $id0, 'user_id' => $userId0, 'description' => $description0, 'secret' => $secret0],
            ['id' => $id1, 'user_id' => $userId1, 'description' => $description1, 'secret' => $secret1],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getPage(0, 10, [], [], []);

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetPageWithOrdersAndConditions()
    {
        $id0          = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $userId0      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description0 = 'foo';
        $secret0      = 'foo-secret';
        $id1          = '51eac0fc-2b26-4231-9559-469e59fae694';
        $userId1      = '27c3e7b4-d88b-4e7a-9f87-d48729842cd7';
        $description1 = 'bar';
        $secret1      = 'bar-secret';

        $orders     = ['ac.description ASC'];
        $conditions = ['ac.description LIKE \'abc%\'', 'abc.description LIKE \'%bca\''];

        $sql          = 'SELECT SQL_CALC_FOUND_ROWS ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted_at IS NULL) AND (ac.description LIKE \'abc%\') AND (abc.description LIKE \'%bca\') GROUP BY ac.id ORDER BY ac.description ASC LIMIT 10 OFFSET 0'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            ['id' => $id0, 'user_id' => $userId0, 'description' => $description0, 'secret' => $secret0],
            ['id' => $id1, 'user_id' => $userId1, 'description' => $description1, 'secret' => $secret1],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getPage(0, 10, $orders, $conditions, []);

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetById()
    {
        $id          = '4b72daf8-81a9-400f-b865-28306d1c1646';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'foo-secret';

        $sql          = 'SELECT ac.id, ac.user_id, ac.description, ac.secret, GROUP_CONCAT(ar.id) AS admin_resource_ids, GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers FROM api_clients AS ac LEFT JOIN api_clients_admin_resources AS acar ON acar.api_client_id = ac.id LEFT JOIN admin_resources AS ar ON acar.admin_resource_id = ar.id WHERE (ac.deleted_at IS NULL) AND (ac.id = :api_client_id) GROUP BY ac.id'; // phpcs:ignore
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

        $sql0       = 'UPDATE api_clients AS api_clients SET description = ?, secret = ? WHERE (id = ?) AND (deleted_at IS NULL)'; // phpcs:ignore
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

    public function testUpdateWithResources()
    {
        $id          = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $userId      = '975c8746-a0e1-43fb-b74c-a331f20d130a';
        $description = 'foo';
        $secret      = 'bar';

        $acarId0     = '4c3c3152-fc34-403e-a83e-7d7ce12b8cc4';
        $resourceId0 = 'e5f9d4b5-92c7-4dac-9feb-c297a7127878';

        $acarId1     = '8060491b-909f-4154-8886-62f06267c864';
        $resourceId1 = '5a2feca1-42dd-4bc4-81b3-9ff55da83e95';

        $this->idGeneratorMock->expects($this->at(0))->method('generate')->willReturn($acarId0);
        $this->idGeneratorMock->expects($this->at(1))->method('generate')->willReturn($acarId1);

        $sql0       = 'UPDATE api_clients AS api_clients SET description = ?, secret = ? WHERE (id = ?) AND (deleted_at IS NULL)'; // phpcs:ignore
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

        $resource0 = new AdminResource($resourceId0, '');
        $resource1 = new AdminResource($resourceId1, '');
        $entity    = new ApiClient($id, $userId, $description, $secret, [$resource0, $resource1]);

        $sql2       = 'INSERT INTO api_clients_admin_resources (id, api_client_id, admin_resource_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values2    = [
            [$acarId0, \PDO::PARAM_STR],
            [$id, \PDO::PARAM_STR],
            [$resourceId0, \PDO::PARAM_STR],
        ];
        $statement2 = MockStatementFactory::createWriteStatement($this, $values2);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql2, $statement2, 2);

        $sql3       = 'INSERT INTO api_clients_admin_resources (id, api_client_id, admin_resource_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values3    = [
            [$acarId1, \PDO::PARAM_STR],
            [$id, \PDO::PARAM_STR],
            [$resourceId1, \PDO::PARAM_STR],
        ];
        $statement3 = MockStatementFactory::createWriteStatement($this, $values3);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql3, $statement3, 3);

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

        $this->assertAdminResources($expectedData, $entity);
    }

    /**
     * @param array     $expectedData
     * @param ApiClient $entity
     */
    protected function assertAdminResources(array $expectedData, $entity)
    {
        if (empty($expectedData['admin_resource_ids'])) {
            return;
        }

        $arIds         = explode(',', $expectedData['admin_resource_ids']);
        $arIdentifiers = explode(',', $expectedData['admin_resource_identifiers']);

        foreach ($entity->getAdminResources() as $idx => $ar) {
            $this->assertSame($arIds[$idx], $ar->getId());
            $this->assertSame($arIdentifiers[$idx], $ar->getIdentifier());
        }
    }
}
