<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

use AbterPhp\Admin\Exception\Database;
use AbterPhp\Framework\TestCase\Database\QueryTestCase;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;

class UserAuthLoaderTest extends QueryTestCase
{
    /** @var UserAuthLoader - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new UserAuthLoader($this->connectionPoolMock);
    }

    public function testLoadAll()
    {
        $username            = 'foo';
        $userGroupIdentifier = 'bar';

        $sql          = 'SELECT u.username AS v0, ug.identifier AS v1 FROM users AS u INNER JOIN users_user_groups AS uug ON uug.user_id = u.id AND uug.deleted = 0 INNER JOIN user_groups AS ug ON uug.user_group_id = ug.id AND ug.deleted = 0 WHERE (u.deleted = 0)'; // phpcs:ignore
        $valuesToBind = [];
        $returnValues = [
            [
                'v0' => $username,
                'v1' => $userGroupIdentifier,
            ],
        ];
        $statement    = MockStatementFactory::createReadStatement($this, $valuesToBind, $returnValues);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->loadAll();

        $this->assertEquals($returnValues, $actualResult);
    }

    public function testLoadAllThrowsExceptionIfQueryFails()
    {
        $errorInfo = ['FOO123', 1, 'near AS v0, ar.identifier: hello'];

        $this->expectException(Database::class);
        $this->expectExceptionCode($errorInfo[1]);

        $sql          = 'SELECT u.username AS v0, ug.identifier AS v1 FROM users AS u INNER JOIN users_user_groups AS uug ON uug.user_id = u.id AND uug.deleted = 0 INNER JOIN user_groups AS ug ON uug.user_group_id = ug.id AND ug.deleted = 0 WHERE (u.deleted = 0)'; // phpcs:ignore
        $valuesToBind = [];
        $statement    = MockStatementFactory::createErrorStatement($this, $valuesToBind, $errorInfo);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $this->sut->loadAll();
    }
}
