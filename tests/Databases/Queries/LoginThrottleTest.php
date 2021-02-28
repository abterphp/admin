<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Framework\TestCase\Database\QueryTestCase;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;

class LoginThrottleTest extends QueryTestCase
{
    /** @var LoginThrottle - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new LoginThrottle($this->connectionPoolMock);
    }

    public function testIsLoginAllowedSuccess()
    {
        $ipHash          = 'foo';
        $username        = 'bar';
        $maxFailureCount = 11;

        $sql0         = 'SELECT COUNT(*) AS count FROM login_attempts AS la WHERE (la.ip_hash = ? OR la.username = ?) AND (la.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $returnValue  = 2;
        $statement0   = MockStatementFactory::createReadColumnStatement($this, $valuesToBind, $returnValue);

        $this->readConnectionMock
            ->expects($this->once())
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $actualResult = $this->sut->isLoginAllowed($ipHash, $username, $maxFailureCount);

        $this->assertTrue($actualResult);
    }

    public function testIsLoginAllowedFailure()
    {
        $ipHash          = 'foo';
        $username        = 'bar';
        $maxFailureCount = 11;

        $sql0         = 'SELECT COUNT(*) AS count FROM login_attempts AS la WHERE (la.ip_hash = ? OR la.username = ?) AND (la.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $returnValue  = 14;
        $statement0   = MockStatementFactory::createReadColumnStatement($this, $valuesToBind, $returnValue);

        $this->readConnectionMock
            ->expects($this->exactly(1))
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $actualResult = $this->sut->isLoginAllowed($ipHash, $username, $maxFailureCount);

        $this->assertFalse($actualResult);
    }

    public function testIsLoginAllowedThrowsExceptionIfQueryFails()
    {
        $errorInfo = ['FOO123', 1, 'near AS v0, ar.identifier: hello'];

        $this->expectException(Database::class);
        $this->expectExceptionCode($errorInfo[1]);

        $ipHash          = 'foo';
        $username        = 'bar';
        $maxFailureCount = 11;

        $sql0         = 'SELECT COUNT(*) AS count FROM login_attempts AS la WHERE (la.ip_hash = ? OR la.username = ?) AND (la.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $statement0   = MockStatementFactory::createErrorStatement($this, $valuesToBind, $errorInfo);

        $this->readConnectionMock
            ->expects($this->exactly(1))
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $this->sut->isLoginAllowed($ipHash, $username, $maxFailureCount);
    }

    public function testClear()
    {
        $ipHash   = 'foo';
        $username = 'bar';

        $sql0         = 'DELETE FROM login_attempts WHERE (login_attempts.ip_hash = ?) AND (login_attempts.username = ?) AND (login_attempts.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $statement0   = MockStatementFactory::createWriteStatement($this, $valuesToBind);

        $this->writeConnectionMock
            ->expects($this->exactly(1))
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $this->sut->clear($ipHash, $username);
    }

    public function testClearThrowsExceptionIfQueryFails()
    {
        $errorInfo = ['FOO123', 1, 'near AS v0, ar.identifier: hello'];

        $this->expectException(Database::class);
        $this->expectExceptionCode($errorInfo[1]);

        $ipHash   = 'foo';
        $username = 'bar';

        $sql0         = 'DELETE FROM login_attempts WHERE (login_attempts.ip_hash = ?) AND (login_attempts.username = ?) AND (login_attempts.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $statement0   = MockStatementFactory::createErrorStatement($this, $valuesToBind, $errorInfo);

        $this->writeConnectionMock
            ->expects($this->exactly(1))
            ->method('prepare')
            ->withConsecutive([$sql0])
            ->willReturnOnConsecutiveCalls($statement0);

        $this->sut->clear($ipHash, $username);
    }
}
