<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Orm\DataMappers;

use AbterPhp\Admin\Domain\Entities\Token as Entity;
use DateTimeImmutable;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\QueryBuilders\MySql\QueryBuilder;
use Opulence\QueryBuilders\MySql\SelectQuery;

/** @phan-file-suppress PhanTypeMismatchArgument */
class TokenSqlDataMapper extends SqlDataMapper implements ITokenDataMapper
{
    /**
     * @param Entity $entity
     */
    public function add($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Token entity.');
        }

        $revokedAtTye = $entity->getRevokedAt() === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR;

        $query = (new QueryBuilder())
            ->insert(
                'tokens',
                [
                    'id'            => [$entity->getId(), \PDO::PARAM_STR],
                    'api_client_id' => [$entity->getApiClientId(), \PDO::PARAM_STR],
                    'expires_at'    => [$entity->getExpiresAt(), \PDO::PARAM_STR],
                    'revoked_at'    => [$entity->getRevokedAt(), $revokedAtTye],
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
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Token entity.');
        }

        $query = (new QueryBuilder())
            ->update(
                'tokens',
                'tokens',
                [
                    'deleted' => [1, \PDO::PARAM_INT],
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
     * @throws \Opulence\Orm\OrmException
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
     * @throws \Opulence\Orm\OrmException
     */
    public function getById($id): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('tokens.id = :token_id');

        $sql    = $query->getSql();
        $params = ['token_id' => [$id, \PDO::PARAM_STR]];

        return $this->read($sql, $params, self::VALUE_TYPE_ENTITY, true);
    }

    /**
     * @param string $clientId
     *
     * @return Entity|null
     * @throws \Opulence\Orm\OrmException
     */
    public function getByClientId(string $clientId): ?Entity
    {
        $query = $this->getBaseQuery()->andWhere('tokens.api_client_id = :api_client_id');

        $sql    = $query->getSql();
        $params = ['api_client_id' => [$clientId, \PDO::PARAM_STR]];

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
            throw new \InvalidArgumentException(__CLASS__ . ':' . __FUNCTION__ . ' expects a Token entity.');
        }

        $revokedAtTye = $entity->getRevokedAt() === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR;

        $query = (new QueryBuilder())
            ->update(
                'tokens',
                'tokens',
                [
                    'api_client_id' => [$entity->getApiClientId(), \PDO::PARAM_STR],
                    'expires_at'    => [$entity->getExpiresAt(), \PDO::PARAM_STR],
                    'revoked_at'    => [$entity->getRevokedAt(), $revokedAtTye],
                ]
            )
            ->where('id = ?')
            ->addUnnamedPlaceholderValue($entity->getId(), \PDO::PARAM_STR);

        $statement = $this->writeConnection->prepare($query->getSql());
        $statement->bindValues($query->getParameters());
        $statement->execute();
    }

    /**
     * @param array $data
     *
     * @return Entity
     * @throws \Exception
     */
    protected function loadEntity(array $data): Entity
    {
        $expiresAt = new DateTimeImmutable($data['expires_at']);
        $revokedAt = null;
        if (null !== $data['revoked_at']) {
            $revokedAt = new DateTimeImmutable($data['revoked_at']);
        }

        return new Entity(
            $data['id'],
            $data['api_client_id'],
            $expiresAt,
            $revokedAt
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
                'tokens.id',
                'tokens.api_client_id',
                'tokens.expires_at',
                'tokens.revoked_at'
            )
            ->from('tokens')
            ->where('tokens.deleted = 0');

        return $query;
    }
}
