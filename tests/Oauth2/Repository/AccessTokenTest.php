<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Oauth2\Entity\AccessToken as Entity;
use AbterPhp\Framework\TestCase\Database\QueryTestCase;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Opulence\Orm\Ids\Generators\UuidV4Generator;
use PHPUnit\Framework\MockObject\MockObject;

class AccessTokenTest extends QueryTestCase
{
    /** @var AccessToken - System Under Test */
    protected $sut;

    /** @var UuidV4Generator|MockObject */
    protected $uuidGeneratorMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->uuidGeneratorMock = $this->getMockBuilder(UuidV4Generator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generate'])
            ->getMock();

        $this->sut = new AccessToken($this->uuidGeneratorMock, $this->connectionPoolMock);
    }

    public function testGetNewToken()
    {
        /** @var ClientEntityInterface|MockObject $clientEntityStub */
        $clientEntityStub = $this->getMockBuilder(ClientEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier', 'getName', 'getRedirectUri'])
            ->getMock();

        $scopes         = [];
        $userIdentifier = null;

        $actualResult = $this->sut->getNewToken($clientEntityStub, $scopes, $userIdentifier);

        $this->assertInstanceOf(Entity::class, $actualResult);
    }

    public function testPersistNewAccessToken()
    {
        $tokenId    = 'foo';
        $clientName = 'bar';
        $expiresAt  = new \DateTime();

        $accessTokenEntityMock = $this->createAccessTokenStub($tokenId, $clientName, $expiresAt, []);

        $sql0          = 'INSERT INTO tokens (id, api_client_id, expires_at) VALUES (?, ?, ?)'; // phpcs:ignore
        $valuesToBind0 = [
            [$tokenId, \PDO::PARAM_STR],
            [$clientName, \PDO::PARAM_STR],
            [$expiresAt->format('Y-m-d H:i:s'), \PDO::PARAM_STR],
        ];
        $statement0    = MockStatementFactory::createWriteStatement($this, $valuesToBind0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $this->sut->persistNewAccessToken($accessTokenEntityMock);
    }

    public function testPersistNewAccessTokenWithScopes()
    {
        $tokenId    = 'foo';
        $clientName = 'bar';
        $expiresAt  = new \DateTime();

        $scopeId0 = '0c4554ca-c379-46ab-9389-bfc84790bb46';
        $scopeId1 = 'aa7076d8-02c1-4d79-becc-e754450a392f';

        $scopeIdentifier0 = 'scope-0';
        $scopeIdentifier1 = 'scope-1';

        $scope0 = $this->createScopeStub($scopeId0, $scopeIdentifier0, 0);
        $scope1 = $this->createScopeStub($scopeId1, $scopeIdentifier1, 1);

        $accessTokenEntityMock = $this->createAccessTokenStub(
            $tokenId,
            $clientName,
            $expiresAt,
            [$scope0, $scope1]
        );

        $sql0          = 'INSERT INTO tokens (id, api_client_id, expires_at) VALUES (?, ?, ?)'; // phpcs:ignore
        $valuesToBind0 = [
            [$tokenId, \PDO::PARAM_STR],
            [$clientName, \PDO::PARAM_STR],
            [$expiresAt->format('Y-m-d H:i:s'), \PDO::PARAM_STR],
        ];
        $statement0    = MockStatementFactory::createWriteStatement($this, $valuesToBind0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1          = 'INSERT INTO tokens_admin_resources (id, token_id, admin_resource_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $valuesToBind1 = [
            [$scopeId0, \PDO::PARAM_STR],
            [$tokenId, \PDO::PARAM_STR],
            [$scopeIdentifier0, \PDO::PARAM_STR],
        ];
        $statement1    = MockStatementFactory::createWriteStatement($this, $valuesToBind1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $sql2          = 'INSERT INTO tokens_admin_resources (id, token_id, admin_resource_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $valuesToBind2 = [
            [$scopeId1, \PDO::PARAM_STR],
            [$tokenId, \PDO::PARAM_STR],
            [$scopeIdentifier1, \PDO::PARAM_STR],
        ];
        $statement2    = MockStatementFactory::createWriteStatement($this, $valuesToBind2);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql2, $statement2, 2);

        $this->sut->persistNewAccessToken($accessTokenEntityMock);
    }

    /**
     * @param string $scopeId
     * @param string $identifier
     * @param int    $at
     *
     * @return ScopeEntityInterface
     */
    protected function createScopeStub(string $scopeId, string $identifier, int $at): ScopeEntityInterface
    {
        $this->uuidGeneratorMock->expects($this->at($at))->method('generate')->willReturn($scopeId);

        $scope = $this->getMockBuilder(ScopeEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier', 'jsonSerialize'])
            ->getMock();
        $scope->expects($this->any())->method('getIdentifier')->willReturn($identifier);

        return $scope;
    }

    /**
     * @param string                 $tokenId
     * @param string                 $clientName
     * @param \DateTime              $expiresAt
     * @param ScopeEntityInterface[] $scopes
     *
     * @return AccessTokenEntityInterface|MockObject
     */
    protected function createAccessTokenStub(
        string $tokenId,
        string $clientName,
        \DateTime $expiresAt,
        array $scopes
    ): AccessTokenEntityInterface {
        $clientStub = $this->getMockBuilder(ClientEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getIdentifier', 'getName', 'getRedirectUri'])
            ->getMock();
        $clientStub->expects($this->any())->method('getName')->willReturn($clientName);

        /** @var AccessTokenEntityInterface|MockObject $accessTokenEntityMock */
        $accessTokenEntityMock = $this->getMockBuilder(AccessTokenEntityInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'convertToJWT',
                'getIdentifier',
                'setIdentifier',
                'getExpiryDateTime',
                'setExpiryDateTime',
                'setUserIdentifier',
                'getUserIdentifier',
                'getClient',
                'setClient',
                'addScope',
                'getScopes',
            ])
            ->getMock();

        $accessTokenEntityMock->expects($this->any())->method('getIdentifier')->willReturn($tokenId);
        $accessTokenEntityMock->expects($this->any())->method('getClient')->willReturn($clientStub);
        $accessTokenEntityMock->expects($this->any())->method('getExpiryDateTime')->willReturn($expiresAt);
        $accessTokenEntityMock->expects($this->any())->method('getScopes')->willReturn($scopes);

        return $accessTokenEntityMock;
    }

    public function testRevokeAccessToken()
    {
        $tokenId = 'foo';

        $this->sut->revokeAccessToken($tokenId);

        $this->markTestIncomplete();
    }

    public function testIsAccessTokenRevoked()
    {
        $tokenId = 'foo';

        $actualResult = $this->sut->isAccessTokenRevoked($tokenId);

        $this->assertFalse($actualResult);
    }
}
