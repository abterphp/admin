<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\ApiClient as Entity;
use AbterPhp\Admin\Http\Service\Execute\RepoServiceAbstract;
use AbterPhp\Admin\Orm\ApiClientRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\ApiClient as ValidatorFactory;
use AbterPhp\Framework\Crypto\Crypto;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Orm\IUnitOfWork;

class ApiClient extends RepoServiceAbstract
{
    /** @var Crypto */
    protected $crypto;

    /**
     * ApiClient constructor.
     *
     * @param GridRepo         $repo
     * @param ValidatorFactory $validatorFactory
     * @param IUnitOfWork      $unitOfWork
     * @param IEventDispatcher $eventDispatcher
     * @param Crypto           $crypto
     */
    public function __construct(
        GridRepo $repo,
        ValidatorFactory $validatorFactory,
        IUnitOfWork $unitOfWork,
        IEventDispatcher $eventDispatcher,
        Crypto $crypto
    ) {
        parent::__construct(
            $repo,
            $validatorFactory,
            $unitOfWork,
            $eventDispatcher
        );

        $this->crypto = $crypto;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        $entity = new Entity($entityId, '', '', '');

        return $entity;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param IStringerEntity $entity
     * @param array           $postData
     * @param UploadedFile[]  $fileData
     *
     * @return Entity
     */
    protected function fillEntity(IStringerEntity $entity, array $postData, array $fileData): IStringerEntity
    {
        if (!($entity instanceof Entity)) {
            return $entity;
        }

        $secret      = (string)$postData['secret'];
        $description = (string)$postData['description'];
        $userId      = (string)$postData['user_id'];

        $adminResources = [];
        if (array_key_exists('admin_resource_ids', $postData)) {
            foreach ($postData['admin_resource_ids'] as $id) {
                $adminResources[] = new AdminResource((string)$id, '');
            }
        }

        $entity
            ->setDescription($description)
            ->setUserId($userId)
            ->setAdminResources($adminResources);

        if ($secret) {
            $secret = $this->crypto->prepareSecret($secret);

            $entity->setSecret($this->crypto->hashCrypt($secret));
        }

        return $entity;
    }
}
