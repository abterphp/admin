<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\AdminResource as Entity;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\Orm\OrmException;
use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\InvalidQueryException;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\SelectQuery;

/** @phan-file-suppress PhanTypeMismatchArgument */
class AdminResourceSqlDataMapper extends SqlDataMapper implements IAdminResourceDataMapper
{
    /**
     * @param IStringerEntity $entity
     */
    public function add($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->insert(
                'admin_resources',
                [
                    'id'         => $entity->getId(),
                    'identifier' => $entity->getIdentifier(),
                ]
            );

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws InvalidQueryException
     */
    public function delete($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->update(
                'admin_resources',
                'admin_resources',
                ['deleted_at' => new Expression('NOW()')]
            )
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

        $sql = $query->getSql();

        return $this->read($sql, [], self::VALUE_TYPE_ARRAY);
    }

    /**
     * @param int|string $id
     *
     * @return Entity|null
     * @throws OrmException
     */
    public function getById($id)
    {
        $query = $this->getBaseQuery()->andWhere('ar.id = :admin_resource_id');

        $parameters = [
            'admin_resource_id' => [$id, \PDO::PARAM_STR],
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
        $query = $this->getBaseQuery()->andWhere('ar.identifier = :identifier');

        $parameters = [
            'identifier' => [$identifier, \PDO::PARAM_STR],
        ];

        return $this->read($query->getSql(), $parameters, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $userId
     *
     * @return Entity[]
     * @throws OrmException
     */
    public function getByUserId(string $userId): array
    {
        $query = $this->getBaseQuery()
            ->innerJoin('user_groups_admin_resources', 'ugar', 'ugar.admin_resource_id = ar.id')
            ->innerJoin('user_groups', 'ug', 'ug.id = ugar.user_group_id')
            ->innerJoin('users_user_groups', 'uug', 'uug.user_group_id = ug.id')
            ->andWhere('uug.user_id = :user_id')
            ->groupBy('ar.id');

        $sql    = $query->getSql();
        $params = [
            'user_id' => [$userId, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ARRAY);
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
                'admin_resources',
                'admin_resources',
                [
                    'identifier' => [$entity->getIdentifier(), \PDO::PARAM_STR],
                ]
            )
            ->where('id = ?')
            ->andWhere('deleted_at IS NULL')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
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
            $hash['identifier']
        );
    }

    /**
     * @return SelectQuery
     */
    private function getBaseQuery(): SelectQuery
    {
        return (new QueryBuilder())
            ->select(
                'ar.id',
                'ar.identifier'
            )
            ->from('admin_resources', 'ar')
            ->where('ar.deleted_at IS NULL');
    }
}
