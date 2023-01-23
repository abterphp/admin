<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\Orm\OrmException;
use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\InvalidQueryException;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\SelectQuery;

/**
 * @phan-file-suppress PhanTypeMismatchArgument
 */
class UserGroupSqlDataMapper extends SqlDataMapper implements IUserGroupDataMapper
{
    use IdGeneratorUserTrait;

    public const ADMIN_RESOURCE_IDS = 'admin_resource_ids';

    /**
     * @param IStringerEntity $entity
     */
    public function add($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->insert(
                'user_groups',
                [
                    'id'         => [$entity->getId(), \PDO::PARAM_STR],
                    'identifier' => [$entity->getIdentifier(), \PDO::PARAM_STR],
                    'name'       => [$entity->getName(), \PDO::PARAM_STR],
                ]
            );

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();

        $this->addAdminResources($entity);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws InvalidQueryException
     */
    public function delete($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $this->deleteAdminResources($entity);

        $query = (new QueryBuilder())
            ->update('user_groups', 'user_groups', ['deleted_at' => new Expression('NOW()')])
            ->where('id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @return Entity[]
     * @throws OrmException
     */
    public function getAll(): array
    {
        $query = $this->getBaseQuery();

        return $this->read($query->getSql(), [], self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param int      $limitFrom
     * @param int      $pageSize
     * @param string[] $orders
     * @param array    $conditions
     * @param array    $params
     *
     * @return Entity[]
     * @throws OrmException
     */
    public function getPage(int $limitFrom, int $pageSize, array $orders, array $conditions, array $params): array
    {
        $query = $this->getBaseQuery()
            ->limit($pageSize)
            ->offset($limitFrom);

        if (!$orders) {
            $query->orderBy('ug.created_at ASC');
        }
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
     * @throws OrmException
     */
    public function getById($id)
    {
        $query = $this->getBaseQuery()->andWhere('ug.id = :user_group_id');

        $parameters = [
            'user_group_id' => [$id, \PDO::PARAM_STR],
        ];

        return $this->read($query->getSql(), $parameters, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $identifier
     *
     * @return Entity|null
     * @throws OrmException
     */
    public function getByIdentifier(string $identifier): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('ug.identifier = :identifier');

        $parameters = [
            'identifier' => [$identifier, \PDO::PARAM_STR],
        ];

        return $this->read($query->getSql(), $parameters, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws InvalidQueryException
     */
    public function update($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->update(
                'user_groups',
                'user_groups',
                [
                    'identifier' => [$entity->getIdentifier(), \PDO::PARAM_STR],
                    'name'       => [$entity->getName(), \PDO::PARAM_STR],
                ]
            )
            ->where('id = ?')
            ->andWhere('deleted_at IS NULL')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();

        $this->deleteAdminResources($entity);
        $this->addAdminResources($entity);
    }

    /**
     * @param array $hash
     *
     * @return Entity
     */
    protected function loadEntity(array $hash): Entity
    {
        $adminResources = $this->getAdminResources($hash);

        return new Entity(
            $hash['id'],
            $hash['identifier'],
            $hash['name'],
            $adminResources
        );
    }

    /**
     * @param array $hash
     *
     * @return AdminResource[]
     */
    private function getAdminResources(array $hash): array
    {
        if (empty($hash[static::ADMIN_RESOURCE_IDS])) {
            return [];
        }

        $adminResources = [];
        foreach (explode(',', $hash[static::ADMIN_RESOURCE_IDS]) as $id) {
            $adminResources[] = new AdminResource($id, '');
        }

        return $adminResources;
    }

    /**
     * @return SelectQuery
     */
    private function getBaseQuery(): SelectQuery
    {
        return (new QueryBuilder())
            ->select(
                'ug.id',
                'ug.identifier',
                'ug.name',
                'GROUP_CONCAT(ugar.admin_resource_id) AS admin_resource_ids'
            )
            ->from('user_groups', 'ug')
            ->leftJoin('user_groups_admin_resources', 'ugar', 'ugar.user_group_id = ug.id')
            ->where('ug.deleted_at IS NULL')
            ->groupBy('ug.id');
    }

    /**
     * @param Entity $entity
     *
     * @throws InvalidQueryException
     */
    protected function deleteAdminResources(Entity $entity)
    {
        $query = (new QueryBuilder())
            ->delete('user_groups_admin_resources')
            ->where('user_group_id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @param Entity $entity
     */
    protected function addAdminResources(Entity $entity)
    {
        $idGenerator = $this->getIdGenerator();

        foreach ($entity->getAdminResources() as $adminResource) {
            $query = (new QueryBuilder())
                ->insert(
                    'user_groups_admin_resources',
                    [
                        'id'                => [$idGenerator->generate($entity), \PDO::PARAM_STR],
                        'user_group_id'     => [$entity->getId(), \PDO::PARAM_STR],
                        'admin_resource_id' => [$adminResource->getId(), \PDO::PARAM_STR],
                    ]
                );

            $sql = $query->getSql();

            $statement = $this->writeConnection->prepare($sql);
            $statement->bindValues($query->getParameters());
            $statement->execute();
        }
    }
}
