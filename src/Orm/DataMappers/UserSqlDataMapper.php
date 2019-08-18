<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\MySql\SelectQuery;

/** @phan-file-suppress PhanTypeMismatchArgument */

class UserSqlDataMapper extends SqlDataMapper implements IUserDataMapper
{
    use IdGeneratorUserTrait;

    /**
     * @param Entity $entity
     */
    public function add($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->insert(
                'users',
                [
                    'id'                  => [$entity->getId(), \PDO::PARAM_STR],
                    'username'            => [$entity->getUsername(), \PDO::PARAM_STR],
                    'email'               => [$entity->getEmail(), \PDO::PARAM_STR],
                    'password'            => [$entity->getPassword(), \PDO::PARAM_STR],
                    'user_language_id'    => [$entity->getUserLanguage()->getId(), \PDO::PARAM_STR],
                    'can_login'           => [$entity->canLogin(), \PDO::PARAM_INT],
                    'is_gravatar_allowed' => [$entity->isGravatarAllowed(), \PDO::PARAM_INT],
                ]
            );

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
        $statement->execute();

        $this->addUserGroups($entity);
    }

    /**
     * @param Entity $entity
     *
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function delete($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a User entity.');
        }

        $rand     = rand(0, PHP_INT_MAX);
        $username = sprintf('deleted-%d', $rand);

        $this->deleteUserGroups($entity);

        $query = (new QueryBuilder())
            ->update(
                'users',
                'users',
                [
                    'deleted'  => [1, \PDO::PARAM_INT],
                    'email'    => [sprintf('%s@example.com', $username), \PDO::PARAM_STR],
                    'username' => [$username, \PDO::PARAM_STR],
                    'password' => ['', \PDO::PARAM_STR],
                ]
            )
            ->where('id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
        $statement->execute();
    }

    /**
     * @return Entity[]
     * @throws \Opulence\Orm\OrmException
     */
    public function getAll(): array
    {
        $query = $this->getBaseQuery();

        $sql = $query->getSql();

        return $this->read($sql, [], self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param int      $limitFrom
     * @param int      $pageSize
     * @param string[] $orders
     * @param array    $conditions
     * @param array    $params
     *
     * @return Entity[]
     * @throws \Opulence\Orm\OrmException
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $conditions, array $params): array
    {
        $query = $this->getBaseQuery()
            ->limit($pageSize)
            ->offset($limitFrom);

        foreach ($orders as $order) {
            $query->addOrderBy($order);
        }

        foreach ($conditions as $condition) {
            $query->andWhere($condition);
        }

        $replaceCount = 1;

        $sql = $query->getSql();
        $sql = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $sql, $replaceCount);

        return $this->read($sql, $params, self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param int|string $id
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getById($id): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('users.id = :user_id');

        $parameters = ['user_id' => [$id, \PDO::PARAM_STR]];

        return $this->read($query->getSql(), $parameters, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $identifier
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function find(string $identifier): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('(username = :identifier OR email = :identifier)');

        $sql    = $query->getSql();
        $params = [
            'identifier' => [$identifier, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY);
    }

    /**
     * @param string $clientId
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getByClientId(string $clientId): ?Entity
    {
        $query = $this->getBaseQuery()
            ->innerJoin(
                'api_clients',
                'ac',
                'ac.user_id = users.id AND ac.deleted = 0'
            )
            ->andWhere('ac.id = :client_id');

        $sql    = $query->getSql();
        $params = [
            'client_id' => [$clientId, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $username
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getByUsername(string $username): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('`username` = :username');

        $sql    = $query->getSql();
        $params = [
            'username' => [$username, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $email
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getByEmail(string $email): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('email = :email');

        $sql    = $query->getSql();
        $params = [
            'email' => [$email, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param Entity $entity
     *
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function update($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->update(
                'users',
                'users',
                [
                    'username'            => [$entity->getUsername(), \PDO::PARAM_STR],
                    'email'               => [$entity->getEmail(), \PDO::PARAM_STR],
                    'password'            => [$entity->getPassword(), \PDO::PARAM_STR],
                    'user_language_id'    => [$entity->getUserLanguage()->getId(), \PDO::PARAM_STR],
                    'can_login'           => [$entity->canLogin(), \PDO::PARAM_INT],
                    'is_gravatar_allowed' => [$entity->isGravatarAllowed(), \PDO::PARAM_INT],
                ]
            )
            ->where('id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
        $statement->execute();

        $this->deleteUserGroups($entity);
        $this->addUserGroups($entity);
    }

    /**
     * @param array $data
     *
     * @return Entity
     */
    protected function loadEntity(array $data): Entity
    {
        $userLanguage = new UserLanguage(
            $data['user_language_id'],
            $data['user_language_identifier'],
            ''
        );
        $userGroups   = $this->loadUserGroups($data);

        return new Entity(
            $data['id'],
            $data['username'],
            $data['email'],
            $data['password'],
            (bool)$data['can_login'],
            (bool)$data['is_gravatar_allowed'],
            $userLanguage,
            $userGroups
        );
    }

    /**
     * @param array $data
     *
     * @return UserGroup[]
     */
    protected function loadUserGroups(array $data): array
    {
        if (empty($data['user_group_ids'])) {
            return [];
        }

        $ids         = explode(',', $data['user_group_ids']);
        $identifiers = explode(',', $data['user_group_identifiers']);
        $names       = explode(',', $data['user_group_names']);

        $userGroups = [];
        foreach ($ids as $idx => $userGroupId) {
            $userGroups[] = new UserGroup($userGroupId, $identifiers[$idx], $names[$idx]);
        }

        return $userGroups;
    }

    /**
     * @return SelectQuery
     */
    private function getBaseQuery(): SelectQuery
    {
        /** @var SelectQuery $query */
        $query = (new QueryBuilder())
            ->select(
                'users.id',
                'users.username',
                'users.email',
                'users.password',
                'users.user_language_id',
                'ul.identifier AS user_language_identifier',
                'users.can_login',
                'users.is_gravatar_allowed',
                'GROUP_CONCAT(ug.id) AS user_group_ids',
                'GROUP_CONCAT(ug.identifier) AS user_group_identifiers',
                'GROUP_CONCAT(ug.name) AS user_group_names'
            )
            ->from('users')
            ->innerJoin(
                'user_languages',
                'ul',
                'ul.id = users.user_language_id AND ul.deleted = 0'
            )
            ->leftJoin('users_user_groups', 'uug', 'uug.user_id = users.id AND uug.deleted = 0')
            ->leftJoin('user_groups', 'ug', 'ug.id = uug.user_group_id AND ug.deleted = 0')
            ->groupBy('users.id')
            ->where('users.deleted = 0');

        return $query;
    }

    /**
     * @param Entity $entity
     *
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    protected function deleteUserGroups(Entity $entity)
    {
        $query = (new QueryBuilder())
            ->delete('users_user_groups')
            ->where('user_id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @param Entity $entity
     */
    protected function addUserGroups(Entity $entity)
    {
        $idGenerator = $this->getIdGenerator();

        foreach ($entity->getUserGroups() as $userGroup) {
            $query = (new QueryBuilder())
                ->insert(
                    'users_user_groups',
                    [
                        'id'            => [$idGenerator->generate($entity), \PDO::PARAM_STR],
                        'user_id'       => [$entity->getId(), \PDO::PARAM_STR],
                        'user_group_id' => [$userGroup->getId(), \PDO::PARAM_STR],
                    ]
                );

            $statement = $this->writeConnection->prepare($query->getSql());
            $statement->bindValues($query->getParameters());
            $statement->execute();
        }
    }
}
