<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\LoginAttempt as Entity;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\Orm\OrmException;
use Opulence\QueryBuilders\InvalidQueryException;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\SelectQuery;

/** @phan-file-suppress PhanTypeMismatchArgument */
class LoginAttemptSqlDataMapper extends SqlDataMapper implements ILoginAttemptDataMapper
{
    /**
     * @param IStringerEntity $entity
     */
    public function add($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $ipAddress     = null;
        $ipAddressType = \PDO::PARAM_NULL;
        if ($entity->getIpAddress()) {
            $ipAddress     = $entity->getIpAddress();
            $ipAddressType = \PDO::PARAM_STR;
        }

        $query = (new QueryBuilder())
            ->insert(
                'login_attempts',
                [
                    'id'         => [$entity->getId(), \PDO::PARAM_STR],
                    'ip_hash'    => [$entity->getIpHash(), \PDO::PARAM_STR],
                    'username'   => [$entity->getUsername(), \PDO::PARAM_STR],
                    'ip_address' => [$ipAddress, $ipAddressType],
                ]
            );

        $sql    = $query->getSql();
        $params = $query->getParameters();

        $statement = $this->writeConnection->prepare($sql);
        $statement->bindValues($params);
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
            ->delete('login_attempts', 'login_attempts')
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
        $query = $this->getBaseQuery()->andWhere('login_attempts.id = :login_attempt_id');

        $sql    = $query->getSql();
        $params = [
            'login_attempt_id' => [$id, \PDO::PARAM_STR],
        ];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws InvalidQueryException
     */
    public function update($entity)
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $ipAddressType = $entity->getIpAddress() === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR;

        $query = (new QueryBuilder())
            ->update(
                'login_attempts',
                'login_attempts',
                [
                    'ip_hash'    => [$entity->getIpHash(), \PDO::PARAM_STR],
                    'username'   => [$entity->getUsername(), \PDO::PARAM_STR],
                    'ip_address' => [$entity->getIpAddress(), $ipAddressType],
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
     * @param array $hash
     *
     * @return Entity
     */
    protected function loadEntity(array $hash): Entity
    {
        return new Entity(
            $hash['id'],
            $hash['ip_hash'],
            $hash['username'],
            $hash['ip_address']
        );
    }

    /**
     * @return SelectQuery
     */
    private function getBaseQuery(): SelectQuery
    {
        return (new QueryBuilder())
            ->select(
                'login_attempts.id',
                'login_attempts.ip_hash',
                'login_attempts.username',
                'login_attempts.ip_address'
            )
            ->from('login_attempts');
    }
}
