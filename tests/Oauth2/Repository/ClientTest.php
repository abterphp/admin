<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Repository;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Admin\Oauth2\Entity\Client as Entity;
use AbterPhp\Framework\Crypto\Crypto;
use AbterPhp\Framework\TestCase\Database\QueryTestCase;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use PHPUnit\Framework\MockObject\MockObject;

class ClientTest extends QueryTestCase
{
    /** @var Client - System Under Test */
    protected $sut;

    /** @var Crypto|MockObject */
    protected $cryptoMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->cryptoMock = $this->createMock(Crypto::class);
        $this->cryptoMock->expects($this->any())->method('prepareSecret')->willReturnArgument(0);

        $this->sut = new Client($this->cryptoMock, $this->connectionPoolMock);
    }

    /**
     * @return array
     */
    public function getClientEntitySuccessProvider(): array
    {
        return [
            'no-null'             => ['grant-type-0', 'client-secret-0', false, ['secret' => 'secret-0']],
            'null-grant-type'     => [null, 'client-secret-0', false, ['secret' => 'secret-0']],
            'null-client-secret'  => ['grant-type-0', null, false, ['secret' => 'secret-0']],
            'validate-no-null'    => ['grant-type-0', 'client-secret-0', true, ['secret' => 'secret-0']],
            'validate-grant-null' => [null, 'client-secret-0', true, ['secret' => 'secret-0']],
        ];
    }

    /**
     * @dataProvider getClientEntitySuccessProvider
     *
     * @param string|null $grantType
     * @param string|null $clientSecret
     * @param bool        $mustValidateSecret
     * @param array       $clientData
     */
    public function testGetClientEntitySuccess(
        ?string $grantType,
        ?string $clientSecret,
        bool $mustValidateSecret,
        array $clientData
    ) {
        $clientIdentifier = 'client-0';

        $this->cryptoMock->expects($this->any())->method('verifySecret')->willReturn(true);

        $sql0          = 'SELECT ac.secret FROM api_clients AS ac WHERE (ac.deleted_at IS NULL) AND (ac.id = :clientId)'; // phpcs:ignore
        $valuesToBind0 = [
            'clientId' => [$clientIdentifier, \PDO::PARAM_STR],
        ];
        $statement0    = MockStatementFactory::createReadRowStatement($this, $valuesToBind0, $clientData);

        $this->readConnectionMock
            ->expects($this->exactly(1))
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $actualResult = $this->sut->getClientEntity($clientIdentifier, $grantType, $clientSecret, $mustValidateSecret);

        $this->assertInstanceOf(Entity::class, $actualResult);
    }

    /**
     * @return array
     */
    public function getClientEntityFailureProvider(): array
    {
        return [
            'weird-secret-stored'  => ['foo', false, [], true],
            'no-secret-stored'     => ['foo', false, ['secret' => ''], true],
            'validate-wo-secret-1' => ['', true, ['secret' => 'secret-0'], true],
            'validate-wo-secret-2' => [null, true, ['secret' => 'secret-0'], true],
            'secrets-dont-match'   => ['foo', true, ['secret' => 'secret-0'], false],
        ];
    }

    /**
     * @dataProvider getClientEntityFailureProvider
     *
     * @param string|null $clientSecret
     * @param bool        $mustValidateSecret
     * @param array       $clientData
     * @param bool        $secretsMatch
     */
    public function testGetClientEntityFailure(
        ?string $clientSecret,
        bool $mustValidateSecret,
        array $clientData,
        bool $secretsMatch
    ) {
        $clientIdentifier = 'client-0';
        $grantType        = 'grant-type-0';

        $this->cryptoMock->expects($this->any())->method('verifySecret')->willReturn($secretsMatch);

        $sql0          = 'SELECT ac.secret FROM api_clients AS ac WHERE (ac.deleted_at IS NULL) AND (ac.id = :clientId)'; // phpcs:ignore
        $valuesToBind0 = [
            'clientId' => [$clientIdentifier, \PDO::PARAM_STR],
        ];
        $statement0    = MockStatementFactory::createReadRowStatement($this, $valuesToBind0, $clientData);

        $this->readConnectionMock
            ->expects($this->exactly(1))
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $actualResult = $this->sut->getClientEntity($clientIdentifier, $grantType, $clientSecret, $mustValidateSecret);

        $this->assertNull($actualResult);
    }

    public function testGetClientEntityThrowsExceptionOnDbFailure()
    {
        $expectedCode    = 17;
        $expectedMessage = 'Foo is great before: FROM api_clients AS ac';

        $this->expectException(Database::class);
        $this->expectExceptionCode($expectedCode);
        $this->expectExceptionMessage($expectedMessage);

        $clientSecret       = 'client-secret-0';
        $mustValidateSecret = false;
        $secretsMatch       = true;

        $clientIdentifier = 'client-0';
        $grantType        = 'grant-type-0';

        $this->cryptoMock->expects($this->any())->method('verifySecret')->willReturn($secretsMatch);

        $sql0          = 'SELECT ac.secret FROM api_clients AS ac WHERE (ac.deleted_at IS NULL) AND (ac.id = :clientId)'; // phpcs:ignore
        $valuesToBind0 = [
            'clientId' => [$clientIdentifier, \PDO::PARAM_STR],
        ];
        $errorInfo0    = ['FOO', $expectedCode, $expectedMessage];
        $statement0    = MockStatementFactory::createErrorStatement($this, $valuesToBind0, $errorInfo0);

        $this->readConnectionMock
            ->expects($this->once())
            ->method('prepare')
            ->with($sql0)
            ->willReturn($statement0);

        $this->sut->getClientEntity($clientIdentifier, $grantType, $clientSecret, $mustValidateSecret);
    }
}
