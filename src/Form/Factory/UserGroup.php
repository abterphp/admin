<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserGroup as Entity;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Form\Container\FormGroup;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Element\MultiSelect;
use AbterPhp\Framework\Form\Element\Select;
use AbterPhp\Framework\Form\IForm;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Orm\IEntity;
use Opulence\Sessions\ISession;

class UserGroup extends Base
{
    /** @var AdminResourceRepo */
    protected $adminResourceRepo;

    /**
     * UserGroup constructor.
     *
     * @param ISession          $session
     * @param ITranslator       $translator
     * @param AdminResourceRepo $adminResourceRepo
     */
    public function __construct(ISession $session, ITranslator $translator, AdminResourceRepo $adminResourceRepo)
    {
        parent::__construct($session, $translator);

        $this->adminResourceRepo = $adminResourceRepo;
    }

    /**
     * @param string       $action
     * @param string       $method
     * @param string       $showUrl
     * @param IEntity|null $entity
     *
     * @return IForm
     */
    public function create(string $action, string $method, string $showUrl, ?IEntity $entity = null): IForm
    {
        if (!($entity instanceof Entity)) {
            throw new \InvalidArgumentException(IFormFactory::ERR_MSG_ENTITY_MISSING);
        }

        $this->createForm($action, $method)
            ->addDefaultElements()
            ->addIdentifier($entity)
            ->addName($entity)
            ->addAdminResources($entity)
            ->addDefaultButtons($showUrl);

        $form = $this->form;

        $this->form = null;

        return $form;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addIdentifier(Entity $entity): UserGroup
    {
        $input = new Input(
            'identifier',
            'identifier',
            $entity->getIdentifier()
        );
        $label = new Label('body', 'admin:userGroupIdentifier');

        $this->form[] = new FormGroup($input, $label);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addName(Entity $entity): UserGroup
    {
        $input = new Input(
            'name',
            'name',
            $entity->getName()
        );
        $label = new Label('body', 'admin:userGroupName');

        $this->form[] = new FormGroup($input, $label);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addAdminResources(Entity $entity): UserGroup
    {
        $allAdminResources = $this->getAllAdminResources();
        $adminResourceIds  = $this->getAdminResourceIds($entity);

        $options = $this->createAdminResourceOptions($allAdminResources, $adminResourceIds);

        $this->form[] = new FormGroup(
            $this->createAdminResourceSelect($options),
            $this->createAdminResourceLabel()
        );

        return $this;
    }

    /**
     * @return AdminResource[]
     * @throws \Opulence\Orm\OrmException
     */
    protected function getAllAdminResources(): array
    {
        return $this->adminResourceRepo->getAll();
    }

    /**
     * @param Entity $entity
     *
     * @return int[]
     */
    protected function getAdminResourceIds(Entity $entity): array
    {
        $adminResourceIds = [];
        foreach ($entity->getAdminResources() as $adminResource) {
            $adminResourceIds[] = $adminResource->getId();
        }

        return $adminResourceIds;
    }

    /**
     * @param AdminResource[] $allAdminResources
     * @param int[]           $adminResourceIds
     *
     * @return array
     */
    protected function createAdminResourceOptions(array $allAdminResources, array $adminResourceIds): array
    {
        $options = [];
        foreach ($allAdminResources as $adminResource) {
            $options[] = new Option(
                (string)$adminResource->getId(),
                $adminResource->getIdentifier(),
                in_array($adminResource->getId(), $adminResourceIds, true)
            );
        }

        return $options;
    }

    /**
     * @param Option[] $options
     *
     * @return Select
     */
    protected function createAdminResourceSelect(array $options): Select
    {
        $size = $this->getMultiSelectSize(
            count($options),
            static::MULTISELECT_MIN_SIZE,
            static::MULTISELECT_MAX_SIZE
        );
        $attributes = [Html5::ATTR_SIZE => [$size]];

        $select = new MultiSelect(
            'admin_resource_ids',
            'admin_resource_ids[]',
            [],
            $attributes
        );

        foreach ($options as $option) {
            $select[] = $option;
        }

        return $select;
    }

    /**
     * @return Label
     */
    protected function createAdminResourceLabel(): Label
    {
        return new Label('admin_resource_ids', 'admin:adminResources');
    }

    /**
     * @param int $optionCount
     * @param int $minSize
     * @param int $maxSize
     *
     * @return int
     */
    protected function getMultiSelectSize(int $optionCount, int $minSize, int $maxSize): int
    {
        return (int)max(min($optionCount, $maxSize), $minSize);
    }
}
