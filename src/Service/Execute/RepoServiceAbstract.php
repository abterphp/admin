<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Validation\Factory\IConditional;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Events\EntityChange;
use AbterPhp\Framework\Http\Service\Execute\IRepoService;
use AbterPhp\Framework\Orm\IGridRepo;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Orm\IUnitOfWork;
use Opulence\Orm\OrmException;
use Opulence\Validation\Factories\IValidatorFactory;
use Opulence\Validation\IValidator;

abstract class RepoServiceAbstract implements IRepoService
{
    protected IGridRepo $repo;

    protected IValidatorFactory $validatorFactory;

    protected IUnitOfWork $unitOfWork;

    protected IEventDispatcher $eventDispatcher;

    protected ?IValidator $validator = null;

    /**
     * RepoExecuteAbstract constructor.
     *
     * @param IGridRepo         $repo
     * @param IValidatorFactory $validatorFactory
     * @param IUnitOfWork       $unitOfWork
     * @param IEventDispatcher  $eventDispatcher
     */
    public function __construct(
        IGridRepo $repo,
        IValidatorFactory $validatorFactory,
        IUnitOfWork $unitOfWork,
        IEventDispatcher $eventDispatcher
    ) {
        $this->repo             = $repo;
        $this->validatorFactory = $validatorFactory;
        $this->unitOfWork       = $unitOfWork;
        $this->eventDispatcher  = $eventDispatcher;
    }

    /**
     * @param array $postData
     * @param int   $additionalData
     *
     * @return array
     */
    public function validateForm(array $postData, int $additionalData = self::MIXED): array
    {
        if ($this->getValidator($additionalData)->isValid($postData)) {
            return [];
        }

        return $this->validator->getErrors()->getAll();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param int $additionalData
     *
     * @return IValidator
     */
    protected function getValidator(int $additionalData): IValidator
    {
        if ($this->validator) {
            return $this->validator;
        }

        if ($this->validatorFactory instanceof IConditional) {
            $this->validatorFactory->setAdditionalData($additionalData);
        }

        $this->validator = $this->validatorFactory->createValidator();

        return $this->validator;
    }

    /**
     * @param string[]       $postData
     * @param UploadedFile[] $fileData
     *
     * @return IStringerEntity
     * @throws OrmException
     */
    public function create(array $postData, array $fileData): IStringerEntity
    {
        $entity = $this->fillEntity($this->createEntity(''), $postData, $fileData);

        $this->repo->add($entity);

        $this->commitCreate($entity);

        return $entity;
    }

    /**
     * @param IStringerEntity $entity
     * @param string[]        $postData
     * @param UploadedFile[]  $fileData
     *
     * @return bool
     * @throws OrmException
     */
    public function update(IStringerEntity $entity, array $postData, array $fileData): bool
    {
        $this->fillEntity($entity, $postData, $fileData);

        $this->commitUpdate($entity);

        return true;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @return bool
     * @throws OrmException
     */
    public function delete(IStringerEntity $entity): bool
    {
        $this->repo->delete($entity);

        $this->commitDelete($entity);

        return true;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws OrmException
     */
    protected function commitCreate(IStringerEntity $entity)
    {
        $event = new EntityChange($entity, Event::ENTITY_CREATE);

        $this->eventDispatcher->dispatch(Event::ENTITY_PRE_CHANGE, $event);

        $this->unitOfWork->commit();

        $this->eventDispatcher->dispatch(Event::ENTITY_POST_CHANGE, $event);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws OrmException
     */
    protected function commitUpdate(IStringerEntity $entity)
    {
        $event = new EntityChange($entity, Event::ENTITY_UPDATE);

        $this->eventDispatcher->dispatch(Event::ENTITY_PRE_CHANGE, $event);

        $this->unitOfWork->commit();

        $this->eventDispatcher->dispatch(Event::ENTITY_POST_CHANGE, $event);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws OrmException
     */
    protected function commitDelete(IStringerEntity $entity)
    {
        $event = new EntityChange($entity, Event::ENTITY_DELETE);

        $this->eventDispatcher->dispatch(Event::ENTITY_PRE_CHANGE, $event);

        $this->unitOfWork->commit();

        $this->eventDispatcher->dispatch(Event::ENTITY_POST_CHANGE, $event);
    }

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     * @throws OrmException
     */
    public function retrieveEntity(string $entityId): IStringerEntity
    {
        /** @var IStringerEntity $entity */
        return $this->repo->getById($entityId);
    }

    /**
     * @param int      $offset
     * @param int      $limit
     * @param string[] $orders
     * @param array    $conditions
     * @param array    $params
     *
     * @return IStringerEntity[]
     */
    public function retrieveList(int $offset, int $limit, array $orders, array $conditions, array $params): array
    {
        return $this->repo->getPage($offset, $limit, $orders, $conditions, $params);
    }

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     */
    abstract public function createEntity(string $entityId): IStringerEntity;

    /**
     * @param IStringerEntity $entity
     * @param array           $postData
     * @param UploadedFile[]  $fileData
     *
     * @return IStringerEntity
     */
    abstract protected function fillEntity(IStringerEntity $entity, array $postData, array $fileData): IStringerEntity;
}
