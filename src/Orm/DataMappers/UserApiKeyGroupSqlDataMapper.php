<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserApiKey as Entity;
use AbterPhp\Framework\Orm\DataMappers\IdGeneratorUserTrait;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\MySql\SelectQuery;

class UserApiKeyGroupSqlDataMapper extends SqlDataMapper implements IUserApiKeyDataMapper
{
    const ADMIN_RESOURCE_IDS = 'admin_resource_ids';

    use IdGeneratorUserTrait;

    /**
     * @param Entity $entity
     */
    public function add($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a User Api Key entity.');
        }

        $query = (new QueryBuilder())
            ->insert(
                'user_api_keys',
                [
                    'id'          => [$entity->getId(), \PDO::PARAM_STR],
                    'user_id'     => [$entity->getUserId(), \PDO::PARAM_STR],
                    'description' => [$entity->getDescription(), \PDO::PARAM_STR],
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
     * @param Entity $entity
     */
    public function delete($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a User Api Key entity.');
        }

        $this->deleteAdminResources($entity);

        $query = (new QueryBuilder())
            ->update('user_api_keys', 'user_api_keys', ['deleted' => [1, \PDO::PARAM_INT]])
            ->where('id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @return array
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
     */
    public function getById($id)
    {
        $query = $this->getBaseQuery()->andWhere('uak.id = :user_api_key_id');

        $parameters = [
            'user_api_key_id' => [$id, \PDO::PARAM_STR],
        ];

        return $this->read($query->getSql(), $parameters, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param Entity $entity
     */
    public function update($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a User Api Key entity.');
        }

        $query = (new QueryBuilder())
            ->update(
                'user_api_keys',
                'user_api_keys',
                [
                    'description' => [$entity->getDescription(), \PDO::PARAM_STR],
                ]
            )
            ->where('id = ?')
            ->andWhere('deleted = 0')
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
    protected function loadEntity(array $hash)
    {
        $adminResources = $this->loadAdminResources($hash);

        return new Entity(
            $hash['id'],
            $hash['user_id'],
            $hash['description'],
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
        /** @var SelectQuery $query */
        $query = (new QueryBuilder())
            ->select(
                'uak.id',
                'uak.user_id',
                'uak.description',
                'GROUP_CONCAT(ar.id) AS admin_resource_ids',
                'GROUP_CONCAT(ar.identifier) AS admin_resource_identifiers'
            )
            ->from('user_api_keys', 'uak')
            ->leftJoin('user_api_keys_admin_resources', 'uakar', 'uakar.user_api_key_id = uak.id')
            ->leftJoin('admin_resources', 'ar', 'uakar.admin_resource_id = ar.id')
            ->where('uak.deleted = 0')
            ->groupBy('uak.id');

        return $query;
    }

    /**
     * @param Entity $entity
     */
    protected function deleteAdminResources(Entity $entity)
    {
        $query = (new QueryBuilder())
            ->delete('user_api_keys_admin_resources')
            ->where('user_api_key_id = ?')
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
                    'user_api_keys_admin_resources',
                    [
                        'id'                => [$idGenerator->generate($entity), \PDO::PARAM_STR],
                        'user_api_key_id'   => [$entity->getId(), \PDO::PARAM_STR],
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
