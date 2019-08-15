<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Framework\TestCase\Database\QueryTestCase;

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

        $sql          = 'SELECT COUNT(*) AS count FROM login_attempts AS la WHERE (la.ip_hash = ? OR la.username = ?) AND (la.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $returnValue  = 2;

        $this->prepare($this->readConnectionMock, $sql, $this->createReadColumnStatement($valuesToBind, $returnValue));

        $actualResult = $this->sut->isLoginAllowed($ipHash, $username, $maxFailureCount);

        $this->assertTrue($actualResult);
    }

    public function testIsLoginAllowedFailure()
    {
        $ipHash          = 'foo';
        $username        = 'bar';
        $maxFailureCount = 11;

        $sql          = 'SELECT COUNT(*) AS count FROM login_attempts AS la WHERE (la.ip_hash = ? OR la.username = ?) AND (la.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];
        $returnValue  = 14;

        $this->prepare($this->readConnectionMock, $sql, $this->createReadColumnStatement($valuesToBind, $returnValue));

        $actualResult = $this->sut->isLoginAllowed($ipHash, $username, $maxFailureCount);

        $this->assertFalse($actualResult);
    }

    public function testClear()
    {
        $ipHash   = 'foo';
        $username = 'bar';

        $sql          = 'DELETE FROM login_attempts WHERE (login_attempts.ip_hash = ?) AND (login_attempts.username = ?) AND (login_attempts.created_at > NOW() - INTERVAL 1 HOUR)'; // phpcs:ignore
        $valuesToBind = [
            [$ipHash, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
        ];

        $this->prepare($this->writeConnectionMock, $sql, $this->createWriteStatement($valuesToBind));

        $actualResult = $this->sut->clear($ipHash, $username);

        $this->assertTrue($actualResult);
    }
}
