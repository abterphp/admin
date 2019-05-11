<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserApiKey as Entity;
use AbterPhp\Admin\Orm\UserApiKeyRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\UserApiKey as ValidatorFactory;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Service\Execute\RepoServiceAbstract;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\IUnitOfWork;

class UserApiKey extends RepoServiceAbstract
{
    /**
     * UserApiKey constructor.
     *
     * @param GridRepo         $repo
     * @param ValidatorFactory $validatorFactory
     * @param IUnitOfWork      $unitOfWork
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        GridRepo $repo,
        ValidatorFactory $validatorFactory,
        IUnitOfWork $unitOfWork,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct(
            $repo,
            $validatorFactory,
            $unitOfWork,
            $eventDispatcher
        );
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        $entity = new Entity($entityId, '', '');

        return $entity;
    }

    /**
     * @param Entity $entity
     * @param array  $data
     *
     * @return Entity
     */
    protected function fillEntity(IStringerEntity $entity, array $data): IStringerEntity
    {
        if (!($entity instanceof Entity)) {
            return $entity;
        }

        $description = (string)$data['description'];
        $userId      = (string)$data['user_id'];

        $adminResources = [];
        if (array_key_exists('admin_resource_ids', $data)) {
            foreach ($data['admin_resource_ids'] as $id) {
                $adminResources[] = new AdminResource((string)$id, '');
            }
        }

        $entity->setDescription($description)->setUserId($userId)->setAdminResources($adminResources);

        return $entity;
    }
}
