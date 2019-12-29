<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMapper;

use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\DataMappers\UserSqlDataMapper;
use AbterPhp\Admin\TestCase\Orm\DataMapperTestCase;
use AbterPhp\Admin\TestDouble\Orm\MockIdGeneratorFactory;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\TestDouble\Database\MockStatementFactory;
use PHPUnit\Framework\MockObject\MockObject;

class UserSqlDataMapperTest extends DataMapperTestCase
{
    /** @var UserSqlDataMapper */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new UserSqlDataMapper($this->readConnectionMock, $this->writeConnectionMock);
    }

    public function testAddWithoutRelated()
    {
        $nextId            = '041532f6-d84c-4827-9404-b426609b99b1';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('c486784f-a067-402b-94f0-fe85c838c71c', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql       = 'INSERT INTO users (id, username, email, password, user_language_id, can_login, is_gravatar_allowed) VALUES (?, ?, ?, ?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
            [$email, \PDO::PARAM_STR],
            [$password, \PDO::PARAM_STR],
            [$userLanguage->getId(), \PDO::PARAM_STR],
            [$canLogin, \PDO::PARAM_INT],
            [$isGravatarAllowed, \PDO::PARAM_INT],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql, $statement);

        $entity = new User($nextId, $username, $email, $password, $canLogin, $isGravatarAllowed, $userLanguage);
        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testAddWithRelated()
    {
        $nextId            = '92c633eb-70f0-4300-930c-6a797f213014';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('5dbea768-0dc9-426e-b434-d225535d440f', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;
        $uugId0            = '76036dfa-5978-434e-96a6-9c5d5e8831c7';
        $uugId1            = '81c6f4a6-583a-41ff-b5bb-9015d9f97bd3';
        $userGroups        = [
            new UserGroup('feaf4cc1-fc70-4468-b34b-738ea17ab94e', 'ug-38', 'UG 38'),
            new UserGroup('47123e47-067e-4542-80b7-a5aa78916a30', 'ug-51', 'UG 51'),
        ];

        $this->sut->setIdGenerator(MockIdGeneratorFactory::create($this, $uugId0, $uugId1));

        $sql0      = 'INSERT INTO users (id, username, email, password, user_language_id, can_login, is_gravatar_allowed) VALUES (?, ?, ?, ?, ?, ?, ?)'; // phpcs:ignore
        $values    = [
            [$nextId, \PDO::PARAM_STR],
            [$username, \PDO::PARAM_STR],
            [$email, \PDO::PARAM_STR],
            [$password, \PDO::PARAM_STR],
            [$userLanguage->getId(), \PDO::PARAM_STR],
            [$canLogin, \PDO::PARAM_INT],
            [$isGravatarAllowed, \PDO::PARAM_INT],
        ];
        $statement = MockStatementFactory::createWriteStatement($this, $values);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement, 0);

        $sql1       = 'INSERT INTO users_user_groups (id, user_id, user_group_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values1    = [
            [$uugId0, \PDO::PARAM_STR],
            [$nextId, \PDO::PARAM_STR],
            [$userGroups[0]->getId(), \PDO::PARAM_STR],
        ];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $sql2       = 'INSERT INTO users_user_groups (id, user_id, user_group_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values2    = [
            [$uugId1, \PDO::PARAM_STR],
            [$nextId, \PDO::PARAM_STR],
            [$userGroups[1]->getId(), \PDO::PARAM_STR],
        ];
        $statement2 = MockStatementFactory::createWriteStatement($this, $values2);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql2, $statement2, 2);

        $entity = new User(
            $nextId,
            $username,
            $email,
            $password,
            $canLogin,
            $isGravatarAllowed,
            $userLanguage,
            $userGroups
        );
        $this->sut->add($entity);

        $this->assertSame($nextId, $entity->getId());
    }

    public function testDelete()
    {
        $id                = '1c36adc9-47e7-4d64-884d-f3c0ef0c89a3';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = 'somePassword';
        $storedPassword    = '';
        $userLanguage      = new UserLanguage('5c66b4ac-5d92-434f-85ee-040f3d1572dc', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql0       = 'DELETE FROM users_user_groups WHERE (user_id = ?)'; // phpcs:ignore
        $values0    = [[$id, \PDO::PARAM_STR]];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1       = 'UPDATE users AS users SET deleted_at = NOW(), username = LEFT(MD5(RAND()), 8), email = CONCAT(username, "@example.com"), password = ? WHERE (id = ?)'; // phpcs:ignore
        $values1    = [
            [$storedPassword, \PDO::PARAM_STR],
            [$id, \PDO::PARAM_STR],
        ];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $entity = new User($id, $username, $email, $password, $canLogin, $isGravatarAllowed, $userLanguage);
        $this->sut->delete($entity);
    }

    public function testGetAll()
    {
        $id                = '85cc9880-4d86-4ffb-8289-98843c0e97eb';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('a6fe61fd-4e03-4ec2-b066-e7d26fbdade3', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) GROUP BY users.id'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetAllWithRelated()
    {
        $id                   = '85cc9880-4d86-4ffb-8289-98843c0e97eb';
        $username             = 'foo';
        $email                = 'foo@example.com';
        $password             = '';
        $userLanguage         = new UserLanguage('a6fe61fd-4e03-4ec2-b066-e7d26fbdade3', 'baz', 'Baz');
        $canLogin             = true;
        $isGravatarAllowed    = true;
        $userGroupIds         = 'd6aae5b8-b34d-409b-9085-e02947e32b8e,aaad5aa6-7044-432d-ae28-2017376a1a55';
        $userGroupIdentifiers = 'ug-1,ug-2';
        $userGroupNames       = 'UG #1,UG #2';

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) GROUP BY users.id'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
                'user_group_ids'           => $userGroupIds,
                'user_group_identifiers'   => $userGroupIdentifiers,
                'user_group_names'         => $userGroupNames,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getAll();

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetPage()
    {
        $id                = '85cc9880-4d86-4ffb-8289-98843c0e97eb';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('a6fe61fd-4e03-4ec2-b066-e7d26fbdade3', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT SQL_CALC_FOUND_ROWS users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) GROUP BY users.id LIMIT 10 OFFSET 0'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getPage(0, 10, [], [], []);

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetPageWithOrdersAndConditions()
    {
        $id                = '85cc9880-4d86-4ffb-8289-98843c0e97eb';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('a6fe61fd-4e03-4ec2-b066-e7d26fbdade3', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $orders     = ['ac.description ASC'];
        $conditions = ['ac.description LIKE \'abc%\'', 'abc.description LIKE \'%bca\''];

        $sql          = 'SELECT SQL_CALC_FOUND_ROWS users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND (ac.description LIKE \'abc%\') AND (abc.description LIKE \'%bca\') GROUP BY users.id ORDER BY ac.description ASC LIMIT 10 OFFSET 0'; // phpcs:ignore
        $values       = [];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getPage(0, 10, $orders, $conditions, []);

        $this->assertCollection($expectedData, $actualResult);
    }

    public function testGetById()
    {
        $id                = 'a83be8c1-fb9a-471f-9284-133487837c46';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('0133699c-7826-4b19-b69a-61b894b924d7', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND (users.id = :user_id) GROUP BY users.id'; // phpcs:ignore
        $values       = ['user_id' => [$id, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getById($id);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testGetByUsername()
    {
        $id                = '792de058-0d5b-4ae3-8e80-6b4470626530';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('e3d2689c-5284-43b4-a847-b84053623b30', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND (`username` = :username) GROUP BY users.id'; // phpcs:ignore
        $values       = ['username' => [$username, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getByUsername($username);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testGetByEmail()
    {
        $id                = 'd26ae765-e801-4444-9bed-af1b825414b3';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('8eb0a9eb-fd6e-4036-a6f6-4da7d99baa65', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND (email = :email) GROUP BY users.id'; // phpcs:ignore
        $values       = ['email' => [$email, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getByEmail($email);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testFindByUsername()
    {
        $id                = 'd9954f10-1ee9-462e-a1e5-6065c032adaa';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('d1284a66-6fad-4e40-a7f6-4c144aa5de5e', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND ((username = :identifier OR email = :identifier)) GROUP BY users.id'; // phpcs:ignore
        $values       = ['identifier' => [$username, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->find($username);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testFindByEmail()
    {
        $id                = '0496d1d1-a750-4c91-822e-03a0fd6718b7';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('8b9543ad-17e8-4e54-af17-e8a8b271a70e', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND ((username = :identifier OR email = :identifier)) GROUP BY users.id'; // phpcs:ignore
        $values       = ['identifier' => [$email, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->find($email);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testGetByClientId()
    {
        $id                = '0496d1d1-a750-4c91-822e-03a0fd6718b7';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('8b9543ad-17e8-4e54-af17-e8a8b271a70e', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;
        $clientId          = '063b730f-3458-49dc-b60e-8171cefacabf';

        $sql          = 'SELECT users.id, users.username, users.email, users.password, users.user_language_id, ul.identifier AS user_language_identifier, users.can_login, users.is_gravatar_allowed, GROUP_CONCAT(ug.id) AS user_group_ids, GROUP_CONCAT(ug.identifier) AS user_group_identifiers, GROUP_CONCAT(ug.name) AS user_group_names FROM users INNER JOIN user_languages AS ul ON ul.id = users.user_language_id AND ul.deleted_at IS NULL INNER JOIN api_clients AS ac ON ac.user_id = users.id AND ac.deleted_at IS NULL LEFT JOIN users_user_groups AS uug ON uug.user_id = users.id AND uug.deleted_at IS NULL LEFT JOIN user_groups AS ug ON ug.id = uug.user_group_id AND ug.deleted_at IS NULL WHERE (users.deleted_at IS NULL) AND (ac.id = :client_id) GROUP BY users.id'; // phpcs:ignore
        $values       = ['client_id' => [$clientId, \PDO::PARAM_STR]];
        $expectedData = [
            [
                'id'                       => $id,
                'username'                 => $username,
                'email'                    => $email,
                'password'                 => $password,
                'user_language_id'         => $userLanguage->getId(),
                'user_language_identifier' => $userLanguage->getIdentifier(),
                'can_login'                => $canLogin,
                'is_gravatar_allowed'      => $isGravatarAllowed,
            ],
        ];

        $statement = MockStatementFactory::createReadStatement($this, $values, $expectedData);
        MockStatementFactory::prepare($this, $this->readConnectionMock, $sql, $statement);

        $actualResult = $this->sut->getByClientId($clientId);

        $this->assertEntity($expectedData[0], $actualResult);
    }

    public function testUpdateWithoutRelated()
    {
        $id                = '76b408bc-d061-4bc7-8e03-8892317e4c78';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('eae6da9c-16fc-4929-8bc6-1a81e2982702', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;

        $sql0       = 'UPDATE users AS users SET username = ?, email = ?, password = ?, user_language_id = ?, can_login = ?, is_gravatar_allowed = ? WHERE (id = ?)'; // phpcs:ignore
        $values0    = [
            [$username, \PDO::PARAM_STR],
            [$email, \PDO::PARAM_STR],
            [$password, \PDO::PARAM_STR],
            [$userLanguage->getId(), \PDO::PARAM_STR],
            [$canLogin, \PDO::PARAM_INT],
            [$isGravatarAllowed, \PDO::PARAM_INT],
            [$id, \PDO::PARAM_STR],
        ];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1       = 'DELETE FROM users_user_groups WHERE (user_id = ?)'; // phpcs:ignore
        $values1    = [[$id, \PDO::PARAM_STR]];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $entity = new User($id, $username, $email, $password, $canLogin, $isGravatarAllowed, $userLanguage);
        $this->sut->update($entity);
    }

    public function testUpdateWithRelated()
    {
        $id                = '2410891a-5e7f-4b39-ad00-5d0379b54b25';
        $username          = 'foo';
        $email             = 'foo@example.com';
        $password          = '';
        $userLanguage      = new UserLanguage('aa489d5d-c3bd-4039-b431-4aaa2d33418a', 'baz', 'Baz');
        $canLogin          = true;
        $isGravatarAllowed = true;
        $uugId0            = '76036dfa-5978-434e-96a6-9c5d5e8831c7';
        $uugId1            = '81c6f4a6-583a-41ff-b5bb-9015d9f97bd3';
        $userGroups        = [
            new UserGroup('7858a040-5b45-4989-b0ea-941a668e9db8', 'ug-38', 'UG 38'),
            new UserGroup('e4ecde09-2b7a-4445-be68-abd5be948d28', 'ug-51', 'UG 51'),
        ];

        $this->sut->setIdGenerator(MockIdGeneratorFactory::create($this, $uugId0, $uugId1));

        $sql0       = 'UPDATE users AS users SET username = ?, email = ?, password = ?, user_language_id = ?, can_login = ?, is_gravatar_allowed = ? WHERE (id = ?)'; // phpcs:ignore
        $values0    = [
            [$username, \PDO::PARAM_STR],
            [$email, \PDO::PARAM_STR],
            [$password, \PDO::PARAM_STR],
            [$userLanguage->getId(), \PDO::PARAM_STR],
            [$canLogin, \PDO::PARAM_INT],
            [$isGravatarAllowed, \PDO::PARAM_INT],
            [$id, \PDO::PARAM_STR],
        ];
        $statement0 = MockStatementFactory::createWriteStatement($this, $values0);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql0, $statement0, 0);

        $sql1       = 'DELETE FROM users_user_groups WHERE (user_id = ?)'; // phpcs:ignore
        $values1    = [[$id, \PDO::PARAM_STR]];
        $statement1 = MockStatementFactory::createWriteStatement($this, $values1);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql1, $statement1, 1);

        $sql2       = 'INSERT INTO users_user_groups (id, user_id, user_group_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values2    = [[$uugId0, \PDO::PARAM_STR], [$id, \PDO::PARAM_STR], [$userGroups[0]->getId(), \PDO::PARAM_STR]];
        $statement2 = MockStatementFactory::createWriteStatement($this, $values2);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql2, $statement2, 2);

        $sql3       = 'INSERT INTO users_user_groups (id, user_id, user_group_id) VALUES (?, ?, ?)'; // phpcs:ignore
        $values3    = [[$uugId1, \PDO::PARAM_STR], [$id, \PDO::PARAM_STR], [$userGroups[1]->getId(), \PDO::PARAM_STR]];
        $statement3 = MockStatementFactory::createWriteStatement($this, $values3);
        MockStatementFactory::prepare($this, $this->writeConnectionMock, $sql3, $statement3, 3);

        $entity = new User(
            $id,
            $username,
            $email,
            $password,
            $canLogin,
            $isGravatarAllowed,
            $userLanguage,
            $userGroups
        );
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
     * @param User  $entity
     */
    protected function assertEntity(array $expectedData, $entity)
    {
        $this->assertInstanceOf(User::class, $entity);
        $this->assertSame($expectedData['id'], $entity->getId());
        $this->assertSame($expectedData['username'], $entity->getUsername());
        $this->assertSame($expectedData['email'], $entity->getEmail());
        $this->assertSame($expectedData['password'], $entity->getPassword());
        $this->assertSame($expectedData['user_language_id'], $entity->getUserLanguage()->getId());
        $this->assertSame($expectedData['user_language_identifier'], $entity->getUserLanguage()->getIdentifier());
        $this->assertSame($expectedData['can_login'], $entity->canLogin());
        $this->assertSame($expectedData['is_gravatar_allowed'], $entity->isGravatarAllowed());

        $this->assertUserGroups($expectedData, $entity);
    }

    /**
     * @param array $expectedData
     * @param User  $entity
     */
    protected function assertUserGroups(array $expectedData, $entity)
    {
        if (empty($expectedData['user_group_ids'])) {
            return;
        }

        $ugIds         = explode(',', $expectedData['user_group_ids']);
        $ugIdentifiers = explode(',', $expectedData['user_group_identifiers']);
        $ugNames       = explode(',', $expectedData['user_group_names']);

        foreach ($entity->getUserGroups() as $idx => $ug) {
            $this->assertSame($ugIds[$idx], $ug->getId());
            $this->assertSame($ugIdentifiers[$idx], $ug->getIdentifier());
            $this->assertSame($ugNames[$idx], $ug->getName());
        }
    }
}
