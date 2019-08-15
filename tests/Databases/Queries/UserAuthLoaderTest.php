<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Databases\Queries;

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
}
