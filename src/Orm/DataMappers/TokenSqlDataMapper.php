<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\Token as Entity;
use AbterPhp\Framework\Orm\DataMappers\IdGeneratorUserTrait;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\MySql\SelectQuery;

class TokenSqlDataMapper extends SqlDataMapper implements ITokenDataMapper
{
    use IdGeneratorUserTrait;

    /**
     * @param Entity $entity
     */
    public function add($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Token entity.');
        }

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

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();

        $this->addUserGroups($entity);
    }

    /**
     * @param Entity $entity
     */
    public function delete($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Token entity.');
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

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @return Entity[]
     */
    public function getAll(): array
    {
        $query = $this->getBaseQuery();

        $sql = $query->getSql();

        return $this->read($sql, [], self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param int|string $id
     *
     * @return Entity|null
     */
    public function getById($id): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('users.id = :user_id');

        $sql    = $query->getSql();
        $params = ['user_id' => [$id, \PDO::PARAM_STR]];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $clientId
     *
     * @return Entity|null
     */
    public function getByClientId(string $clientId): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('`api_client_id` = :api_client_id');

        $sql    = $query->getSql();
        $params = ['api_client_id' => [$clientId, \PDO::PARAM_STR]];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param Entity $entity
     */
    public function update($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Token entity.');
        }

        $query = (new QueryBuilder())
            ->update(
                'tokens',
                'tokens',
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

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();

        $this->deleteUserGroups($entity);
        $this->addUserGroups($entity);
    }

    /**
     * @param Entity $entity
     * @param bool   $create
     *
     * @return array
     */
    protected function getColumnNamesToValues(Entity $entity, bool $create): array
    {
        $columnNamesToValues = [
            'name'       => [$entity->getName(), \PDO::PARAM_STR],
            'identifier' => [$entity->getIdentifier(), \PDO::PARAM_STR],
        ];

        if ($create) {
            $columnNamesToValues = array_merge(['id' => [$entity->getId(), \PDO::PARAM_STR]], $columnNamesToValues);
        }

        return $columnNamesToValues;
    }

    /**
     * @param array $data
     *
     * @return Entity
     */
    protected function loadEntity(array $data): Entity
    {
        if (empty($data['id'])) {
            throw new \RuntimeException();
        }

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

        if (count($ids) !== count($identifiers) || count($ids) !== count($names)) {
            throw new \LogicException();
        }

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
