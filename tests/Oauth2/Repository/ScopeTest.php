<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Admin\Oauth2\Entity\Scope as Entity;
use AbterPhp\Framework\TestCase\Database\QueryTestCase;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ScopeTest extends QueryTestCase
{
    /** @var Scope - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Scope($this->connectionPoolMock);
    }

    public function testGetScopeEntityByIdentifier()
    {
        $id         = 'c9f0176b-d6f2-4f31-802f-67ad253f9fe7';
        $identifier = 'foo';

        $sql0          = 'SELECT ar.id FROM admin_resources AS ar WHERE (ar.deleted = 0) AND (ar.identifier = :identifier)'; // phpcs:ignore
        $valuesToBind0 = [
            'identifier' => [$identifier, \PDO::PARAM_STR],
        ];
        $row0          = ['id' => $id];
        $statement0    = MockStatementFactory::createReadRowStatement($this, $valuesToBind0, $row0);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql0, $statement0, 0);

        $actualResult = $this->sut->getScopeEntityByIdentifier($identifier);

        $this->assertInstanceOf(ScopeEntityInterface::class, $actualResult);
    }

    public function testGetScopeEntityByIdentifierThrowsExceptionOnFailure()
    {
        $expectedCode    = 17;
        $expectedMessage = 'Foo is great before: FROM api_clients AS ac';

        $this->expectException(Database::class);
        $this->expectExceptionCode($expectedCode);
        $this->expectExceptionMessage($expectedMessage);

        $id         = 'c9f0176b-d6f2-4f31-802f-67ad253f9fe7';
        $identifier = 'foo';

        $sql0          = 'SELECT ar.id FROM admin_resources AS ar WHERE (ar.deleted = 0) AND (ar.identifier = :identifier)'; // phpcs:ignore
        $valuesToBind0 = [
            'identifier' => [$identifier, \PDO::PARAM_STR],
        ];
        $errorInfo0    = ['FOO', $expectedCode, $expectedMessage];
        $statement0    = MockStatementFactory::createErrorStatement($this, $valuesToBind0, $errorInfo0);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql0, $statement0, 0);

        $this->sut->getScopeEntityByIdentifier($identifier);
    }

    public function testFinalizeScopesWithoutScopes()
    {
        $grantType = 'foo';
        $scopes    = [];

        /** @var ClientEntityInterface|MockObject $clientEntityMock */
        $clientEntityMock = $this->getMockBuilder(ClientEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier', 'getName', 'getRedirectUri'])
            ->getMock();

        $actualResult = $this->sut->finalizeScopes($scopes, $grantType, $clientEntityMock, null);

        $this->assertSame([], $actualResult);
    }

    public function testFinalizeScopesWithScopes()
    {
        $clientIdentifier = 'quix';
        $grantType        = 'foo';

        $scopeIdentifier0 = 'bar';
        $scopeIdentifier1 = 'baz';
        $scopes           = [
            $this->createScopeStub($scopeIdentifier0),
            $this->createScopeStub($scopeIdentifier1),
        ];

        $arId0 = 'cfa57635-bed4-4f59-a7c8-091fb0c05964';
        $arId1 = 'ddd844f3-6894-4049-821d-3ed461e2e970';
        $arId2 = '3966099c-84ff-48cf-9d65-794519651fe5';

        /** @var ClientEntityInterface|MockObject $clientEntityMock */
        $clientEntityMock = $this->getMockBuilder(ClientEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier', 'getName', 'getRedirectUri'])
            ->getMock();
        $clientEntityMock->expects($this->any())->method('getIdentifier')->willReturn($clientIdentifier);

        $sql0          = 'SELECT acar.admin_resource_id FROM api_clients_admin_resources AS acar WHERE (acar.api_client_id = ?) AND (acar.admin_resource_id IN (?,?))'; // phpcs:ignore
        $valuesToBind0 = [
            [$clientIdentifier, \PDO::PARAM_STR],
            [$scopeIdentifier0, \PDO::PARAM_STR],
            [$scopeIdentifier1, \PDO::PARAM_STR],
        ];
        $rows0         = [
            ['admin_resource_id' => $arId0],
            ['admin_resource_id' => $arId1],
            ['admin_resource_id' => $arId2],
        ];
        $statement0    = MockStatementFactory::createReadStatement($this, $valuesToBind0, $rows0);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql0, $statement0, 0);

        $actualResult = $this->sut->finalizeScopes($scopes, $grantType, $clientEntityMock, null);

        $this->assertCount(3, $actualResult);
        $this->assertSame($arId0, $actualResult[0]->getIdentifier());
        $this->assertSame($arId1, $actualResult[1]->getIdentifier());
        $this->assertSame($arId2, $actualResult[2]->getIdentifier());
    }

    public function testFinalizeScopesWithScopesThrowsExceptionOnFailure()
    {
        $expectedCode    = 17;
        $expectedMessage = 'Foo is great before: FROM api_clients AS ac';

        $this->expectException(Database::class);
        $this->expectExceptionCode($expectedCode);
        $this->expectExceptionMessage($expectedMessage);

        $clientIdentifier = 'quix';
        $grantType        = 'foo';

        $scopeIdentifier0 = 'bar';
        $scopeIdentifier1 = 'baz';
        $scopes           = [
            $this->createScopeStub($scopeIdentifier0),
            $this->createScopeStub($scopeIdentifier1),
        ];

        $arId0 = 'cfa57635-bed4-4f59-a7c8-091fb0c05964';
        $arId1 = 'ddd844f3-6894-4049-821d-3ed461e2e970';
        $arId2 = '3966099c-84ff-48cf-9d65-794519651fe5';

        /** @var ClientEntityInterface|MockObject $clientEntityMock */
        $clientEntityMock = $this->getMockBuilder(ClientEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier', 'getName', 'getRedirectUri'])
            ->getMock();
        $clientEntityMock->expects($this->any())->method('getIdentifier')->willReturn($clientIdentifier);

        $sql0          = 'SELECT acar.admin_resource_id FROM api_clients_admin_resources AS acar WHERE (acar.api_client_id = ?) AND (acar.admin_resource_id IN (?,?))'; // phpcs:ignore
        $valuesToBind0 = [
            [$clientIdentifier, \PDO::PARAM_STR],
            [$scopeIdentifier0, \PDO::PARAM_STR],
            [$scopeIdentifier1, \PDO::PARAM_STR],
        ];
        $errorInfo0    = ['FOO', $expectedCode, $expectedMessage];
        $statement0    = MockStatementFactory::createErrorStatement($this, $valuesToBind0, $errorInfo0);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql0, $statement0, 0);

        $this->sut->finalizeScopes($scopes, $grantType, $clientEntityMock, null);
    }

    /**
     * @param string $identifier
     *
     * @return Entity|MockObject
     */
    protected function createScopeStub(string $identifier): Entity
    {
        $scopeStub = $this->getMockBuilder(Entity::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier'])
            ->getMock();
        $scopeStub->expects($this->any())->method('getIdentifier')->willReturn($identifier);

        return $scopeStub;
    }
}
