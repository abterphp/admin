<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\UserApiKey as Entity;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Form\Container\FormGroup;
use AbterPhp\Framework\Form\Element\MultiSelect;
use AbterPhp\Framework\Form\Element\Select;
use AbterPhp\Framework\Form\Element\Textarea;
use AbterPhp\Framework\Form\Factory\Base;
use AbterPhp\Framework\Form\Factory\IFormFactory;
use AbterPhp\Framework\Form\IForm;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Orm\IEntity;
use Opulence\Sessions\ISession;

class UserApiKey extends Base
{
    /** @var AdminResourceRepo */
    protected $adminResourceRepo;

    /**
     * UserApiKey constructor.
     *
     * @param ISession          $session
     * @param ITranslator       $translator
     * @param AdminResourceRepo $adminResourceRepo
     */
    public function __construct(
        ISession $session,
        ITranslator $translator,
        AdminResourceRepo $adminResourceRepo
    ) {
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
            ->addDescription($entity)
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
    protected function addDescription(Entity $entity): UserApiKey
    {
        $input = new Textarea(
            'description',
            'description',
            $entity->getDescription()
        );
        $label = new Label('description', 'admin:userApiKeyDescription');

        $this->form[] = new FormGroup($input, $label);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addAdminResources(Entity $entity): UserApiKey
    {
        $allUserResources = $this->getUserResources();

        $existingData = [];
        foreach ($entity->getAdminResources() as $adminResource) {
            $existingData[$adminResource->getId()] = $adminResource->getIdentifier();
        }

        $options = $this->createAdminResourceOptions($allUserResources, $existingData);

        $this->form[] = new FormGroup(
            $this->createAdminResourceSelect($options),
            $this->createAdminResourceLabel()
        );

        return $this;
    }

    /**
     * @return UserGroup[]
     */
    protected function getUserResources(): array
    {
        $userId = $this->session->get(Session::USER_ID);

        return $this->adminResourceRepo->getByUserId($userId);
    }

    /**
     * @param AdminResource[] $allUserResources
     * @param string[]        $existingData
     *
     * @return array
     */
    protected function createAdminResourceOptions(array $allUserResources, array $existingData): array
    {
        $existingIds = array_keys($existingData);

        $options = [];
        foreach ($allUserResources as $userResources) {
            $isSelected = in_array($userResources->getId(), $existingIds, true);
            $options[]  = new Option($userResources->getId(), $userResources->getIdentifier(), $isSelected);
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
        $attributes = [
            Html5::ATTR_SIZE => $this->getMultiSelectSize(
                count($options),
                static::MULTISELECT_MIN_SIZE,
                static::MULTISELECT_MAX_SIZE
            ),
        ];

        $select = new MultiSelect('admin_resource_ids', 'admin_resource_ids[]', [], $attributes);

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
}
