<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Admin\Orm\UserGroupRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\UserGroup as ValidatorFactory;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Service\Execute\RepoServiceAbstract;
use Cocur\Slugify\Slugify;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Orm\IUnitOfWork;

class UserGroup extends RepoServiceAbstract
{
    /** @var Slugify */
    protected $slugify;

    /**
     * UserGroup constructor.
     *
     * @param GridRepo         $repo
     * @param ValidatorFactory $validatorFactory
     * @param IUnitOfWork      $unitOfWork
     * @param IEventDispatcher $eventDispatcher
     * @param Slugify          $slugify
     */
    public function __construct(
        GridRepo $repo,
        ValidatorFactory $validatorFactory,
        IUnitOfWork $unitOfWork,
        IEventDispatcher $eventDispatcher,
        Slugify $slugify
    ) {
        parent::__construct($repo, $validatorFactory, $unitOfWork, $eventDispatcher);

        $this->slugify = $slugify;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        return new Entity($entityId, '', '');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Entity         $entity
     * @param array          $postData
     * @param UploadedFile[] $fileData
     *
     * @return Entity
     */
    protected function fillEntity(IStringerEntity $entity, array $postData, array $fileData): IStringerEntity
    {
        if (!($entity instanceof Entity)) {
            return $entity;
        }

        $name = isset($postData['name']) ? (string)$postData['name'] : '';

        $identifier = $this->slugify->slugify($name);

        $adminResources = [];
        if (array_key_exists('admin_resource_ids', $postData)) {
            foreach ($postData['admin_resource_ids'] as $id) {
                $adminResources[] = new AdminResource((string)$id, '');
            }
        }

        $entity
            ->setName($name)
            ->setIdentifier($identifier)
            ->setAdminResources($adminResources);

        return $entity;
    }
}
