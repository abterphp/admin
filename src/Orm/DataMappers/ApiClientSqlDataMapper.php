<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\ApiClient as Entity;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\Orm\OrmException;
use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\InvalidQueryException;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\SelectQuery;

/** @phan-file-suppress PhanTypeMismatchArgument */

class ApiClientSqlDataMapper extends SqlDataMapper implements IApiClientDataMapper
{
    use IdGeneratorUserTrait;

    /**
     * @param IStringerEntity $entity
     */
    public function add($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $query = (new QueryBuilder())
            ->insert(
                'api_clients',
                [
                    'id'          => [$entity->getId(), \PDO::PARAM_STR],
                    'user_id'     => [$entity->getUserId(), \PDO::PARAM_STR],
                    'description' => [$entity->getDescription(), \PDO::PARAM_STR],
                    'secret'      => [$entity->getSecret(), \PDO::PARAM_STR],
                ]
            );

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
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
            ->update('api_clients', 'api_clients', ['deleted_at' => new Expression('NOW()')])
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
            $query->orderBy('created_at ASC');
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
        $query = $this->getBaseQuery()->andWhere('ac.id = :api_client_id');

        $parameters = [
            'api_client_id' => [$id, \PDO::PARAM_STR],
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
                'api_clients',
                'api_clients',
                [
                    'description' => [$entity->getDescription(), \PDO::PARAM_STR],
                    'secret'      => [$entity->getSecret(), \PDO::PARAM_STR],
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
        $adminResources = $this->loadAdminResources($hash);

        return new Entity(
            $hash['id'],
            $hash['user_id'],
            $hash['description'],
            $hash['secret'],
            $adminResources
        );
    }

    /**
     * @param array $hash
     *
     * @return array
     */
    protected function loadAdminResources(array $hash): array
    {
        if (empty($hash['admin_resource_ids'])) {
            return [];
        }

        $adminResourceIds         = explode(',', $hash['admin_resource_ids']);
        $adminResourceIdentifiers = explode(',', $hash['admin_resource_identifiers']);

        $adminResources = [];
        foreach ($adminResourceIds as $idx => $adminResourceId) {
            $adminResources[] = new AdminResource($adminResourceId, $adminResourceIdentifiers[$idx]);
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
                'ac.id',
                'ac.user_id',
                'ac.description',
                'ac.secret',
                'GROUP_CONCAT(ar.id) AS admin_resource_ids',
                'GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers'
            )
            ->from('api_clients', 'ac')
            ->leftJoin('api_clients_admin_resources', 'acar', 'acar.api_client_id = ac.id')
            ->leftJoin('admin_resources', 'ar', 'acar.admin_resource_id = ar.id')
            ->where('ac.deleted_at IS NULL')
            ->groupBy('ac.id');
    }

    /**
     * @param Entity $entity
     *
     * @throws InvalidQueryException
     */
    protected function deleteAdminResources(Entity $entity)
    {
        $query = (new QueryBuilder())
            ->delete('api_clients_admin_resources')
            ->where('api_client_id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
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
                    'api_clients_admin_resources',
                    [
                        'id'                => [$idGenerator->generate($entity), \PDO::PARAM_STR],
                        'api_client_id'     => [$entity->getId(), \PDO::PARAM_STR],
                        'admin_resource_id' => [$adminResource->getId(), \PDO::PARAM_STR],
                    ]
                );

            $sql    = $query->getSql();
            $params = $query->getParameters();

            $statement = $this->writeConnection->prepare($sql);
            $statement->bindValues($params);
            $statement->execute();
        }
    }
}
