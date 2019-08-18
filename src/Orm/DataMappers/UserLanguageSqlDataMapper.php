<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\UserLanguage as Entity;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\MySql\SelectQuery;

/** @phan-file-suppress PhanTypeMismatchArgument */

class UserLanguageSqlDataMapper extends SqlDataMapper implements IUserLanguageDataMapper
{
    /**
     * @param Entity $entity
     */
    public function add($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a UserLanguage entity.');
        }

        $query = (new QueryBuilder())
            ->insert(
                'user_languages',
                [
                    'id'         => $entity->getId(),
                    'identifier' => $entity->getIdentifier(),
                    'name'       => $entity->getName(),
                ]
            );

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @param Entity $entity
     *
     * @throws \Opulence\QueryBuilders\InvalidQueryException
     */
    public function delete($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a UserLanguage entity.');
        }

        $query = (new QueryBuilder())
            ->update('user_languages', 'user_languages', ['deleted' => [1, \PDO::PARAM_INT]])
            ->where('id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
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
     * @param string $id
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getById($id)
    {
        $query = $this->getBaseQuery()->andWhere('user_languages.id = :user_language_id');

        $sql    = $query->getSql();
        $params = [
            'user_language_id' => [$id, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $identifier
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getByIdentifier(string $identifier): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('identifier = :identifier');

        $sql    = $query->getSql();
        $params = [
            'identifier' => [$identifier, \PDO::PARAM_STR],
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
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a UserLanguage entity.');
        }

        $query = (new QueryBuilder())
            ->update(
                'user_languages',
                'user_languages',
                [
                    'identifier' => [$entity->getIdentifier(), \PDO::PARAM_STR],
                    'name'       => [$entity->getName(), \PDO::PARAM_STR],
                ]
            )
            ->where('id = ?')
            ->andWhere('deleted = 0')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
        $statement->execute();
    }

    /**
     * @param array $hash
     *
     * @return Entity
     */
    protected function loadEntity(array $hash): Entity
    {
        return new Entity(
            $hash['id'],
            $hash['identifier'],
            $hash['name']
        );
    }

    /**
     * @return SelectQuery
     */
    private function getBaseQuery(): SelectQuery
    {
        /** @var SelectQuery $query */
        $query = (new QueryBuilder())
            ->select(
                'user_languages.id',
                'user_languages.identifier',
                'user_languages.name'
            )
            ->from('user_languages')
            ->where('user_languages.deleted = 0');

        return $query;
    }
}
