<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\Token;
use AbterPhp\Admin\Orm\DataMappers\TokenSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\DataMapperTestCase;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use PHPUnit\Framework\MockObject\MockObject;

class TokenSqlDataMapperTest extends DataMapperTestCase
{
    /** @var TokenSqlDataMapper */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new TokenSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);
    }

    public function testAdd()
    {
        $nextId      = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt   = new \DateTimeImmutable();
        $revokedAt   = null;

        $sql       = 'INSERT INTO tokens (id, api_client_id, expires_at, revoked_at) VALUES (?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$apiClientId, \PDO::PARAM_STR],
            [$expiresAt, \PDO::PARAM_STR],
            [$revokedAt, \PDO::PARAM_NULL],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new Token($nextId, $apiClientId, $expiresAt, $revokedAt);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testAddWithRevokedAt()
    {
        $nextId      = '9b6ae58b-1aff-4344-a2ae-cda43a40674e';
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt   = new \DateTimeImmutable();
        $revokedAt   = new \DateTimeImmutable();

        $sql       = 'INSERT INTO tokens (id, api_client_id, expires_at, revoked_at) VALUES (?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$apiClientId, \PDO::PARAM_STR],
            [$expiresAt, \PDO::PARAM_STR],
            [$revokedAt, \PDO::PARAM_STR],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new Token($nextId, $apiClientId, $expiresAt, $revokedAt);

        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id          = '8fe2f659-dbe5-4995-9e07-f49fb018cfe7';
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt   = new \DateTimeImmutable();

        $sql       = 'UPDATE tokens AS tokens SET deleted = ? WHERE (id = ?)'; // phpcs:ignore
        $values    = [[1, \PDO::PARAM_INT], [$id, \PDO::PARAM_STR]];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new Token($id, $apiClientId, $expiresAt, null);

        $this->sut->delete($entity);
    }

    public function testGetAll()
    {
        $id0          = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $apiClientId0 = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt0   = '2019-08-18 00:22:03';
        $revokedAt0   = null;

        $id1          = '51eac0fc-2b26-4231-9559-469e59fae694';
        $apiClientId1 = 'd9162b70-03e0-4969-aecc-8112adcd94c0';
        $expiresAt1   = '2019-08-18 00:22:13';
        $revokedAt1   = '2019-08-18 00:22:18';

        $sql          = 'SELECT tokens.id, tokens.api_client_id, tokens.expires_at, tokens.revoked_at FROM tokens WHERE (tokens.deleted = 0)'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            ['id' => $id0, 'api_client_id' => $apiClientId0, 'expires_at' => $expiresAt0, 'revoked_at' => $revokedAt0],
            ['id' => $id1, 'api_client_id' => $apiClientId1, 'expires_at' => $expiresAt1, 'revoked_at' => $revokedAt1],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetByClientId()
    {
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';

        $id0          = '24bd4165-1229-4a6e-a679-76bf90743ee1';
        $apiClientId0 = $apiClientId;
        $expiresAt0   = '2019-08-18 00:22:03';
        $revokedAt0   = null;

        $sql          = 'SELECT tokens.id, tokens.api_client_id, tokens.expires_at, tokens.revoked_at FROM tokens WHERE (tokens.deleted = 0) AND (tokens.api_client_id = :api_client_id)'; // phpcs:ignore
        $values       = [
            'api_client_id' => [$apiClientId, \PDO::PARAM_STR],
        ];
        $expectedData = [
            ['id' => $id0, 'api_client_id' => $apiClientId0, 'expires_at' => $expiresAt0, 'revoked_at' => $revokedAt0],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getByClientId($apiClientId);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testGetById()
    {
        $id          = '4b72daf8-81a9-400f-b865-28306d1c1646';
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt   = '2019-08-18 00:22:03';
        $revokedAt   = null;

        $sql          = 'SELECT tokens.id, tokens.api_client_id, tokens.expires_at, tokens.revoked_at FROM tokens WHERE (tokens.deleted = 0) AND (tokens.id = :token_id)'; // phpcs:ignore
        $values       = ['token_id' => [$id, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'            => $id,
                'api_client_id' => $apiClientId,
                'expires_at'    => $expiresAt,
                'revoked_at'    => $revokedAt,
            ],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getById($id);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testUpdate()
    {
        $id          = '91693481-276e-495b-82a1-33209c47ca09';
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt   = new \DateTimeImmutable('2019-08-18 00:22:03');
        $revokedAt   = null;

        $sql       = 'UPDATE tokens AS tokens SET api_client_id = ?, expires_at = ?, revoked_at = ? WHERE (id = ?)'; // phpcs:ignore
        $values    = [
            [$apiClientId, \PDO::PARAM_STR],
            [$expiresAt, \PDO::PARAM_STR],
            [$revokedAt, \PDO::PARAM_NULL],
            [$id, \PDO::PARAM_STR],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new Token($id, $apiClientId, $expiresAt, $revokedAt);

        $this->sut->update($entity);
    }

    public function testUpdateWithRevokedAt()
    {
        $id          = '91693481-276e-495b-82a1-33209c47ca09';
        $apiClientId = '33a9ef7e-3d84-4bd0-9b38-59b5cb7d5245';
        $expiresAt   = new \DateTimeImmutable('2019-08-18 00:22:03');
        $revokedAt   = new \DateTimeImmutable('2019-08-18 00:22:13');

        $sql       = 'UPDATE tokens AS tokens SET api_client_id = ?, expires_at = ?, revoked_at = ? WHERE (id = ?)'; // phpcs:ignore
        $values    = [
            [$apiClientId, \PDO::PARAM_STR],
            [$expiresAt, \PDO::PARAM_STR],
            [$revokedAt, \PDO::PARAM_STR],
            [$id, \PDO::PARAM_STR],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);
        $entity = new Token($id, $apiClientId, $expiresAt, $revokedAt);

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
     * @param array $expectedData
     * @param Token $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
        $this->assertInstanceOf(Token::class, $entity);
        $this->assertEquals($expectedData['id'], $entity->getId());
        $this->assertSame($expectedData['api_client_id'], $entity->getApiClientId());
        $this->assertEquals(new \DateTimeImmutable($expectedData['expires_at']), $entity->getExpiresAt());

        if (null === $expectedData['revoked_at']) {
            $this->assertNull($entity->getRevokedAt());
        } else {
            $this->assertEquals(new \DateTimeImmutable($expectedData['revoked_at']), $entity->getRevokedAt());
        }
    }
}
