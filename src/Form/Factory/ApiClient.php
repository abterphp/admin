<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\ApiClient as Entity;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Form\Container\FormGroup;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Element\MultiSelect;
use AbterPhp\Framework\Form\Element\Select;
use AbterPhp\Framework\Form\Element\Textarea;
use AbterPhp\Framework\Form\Extra\Help;
use AbterPhp\Framework\Form\Factory\Base;
use AbterPhp\Framework\Form\Factory\IFormFactory;
use AbterPhp\Framework\Form\IForm;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Component\ButtonFactory;
use AbterPhp\Framework\Html\Component\ButtonWithIcon;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Orm\IEntity;
use Opulence\Sessions\ISession;

class ApiClient extends Base
{
    /** @var AdminResourceRepo */
    protected $adminResourceRepo;

    /** @var ButtonFactory */
    protected $buttonFactory;

    /**
     * ApiClient constructor.
     *
     * @param ISession          $session
     * @param ITranslator       $translator
     * @param AdminResourceRepo $adminResourceRepo
     * @param ButtonFactory     $buttonFactory
     */
    public function __construct(
        ISession $session,
        ITranslator $translator,
        AdminResourceRepo $adminResourceRepo,
        ButtonFactory $buttonFactory
    ) {
        parent::__construct($session, $translator);

        $this->adminResourceRepo = $adminResourceRepo;
        $this->buttonFactory     = $buttonFactory;
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
            ->addJsOnly()
            ->addDefaultElements()
            ->addId($entity)
            ->addDescription($entity)
            ->addAdminResources($entity)
            ->addSecret($entity)
            ->addDefaultButtons($showUrl);

        $form = $this->form;

        $this->form = null;

        return $form;
    }

    /**
     * @return $this
     */
    protected function addJsOnly(): ApiClient
    {
        $content    = sprintf(
            '<i class="material-icons">warning</i>&nbsp;%s',
            $this->translator->translate('admin:jsOnly')
        );
        $attributes = [Html5::ATTR_CLASS => 'only-js-form-warning'];

        $this->form[] = new Component($content, [], $attributes, Html5::TAG_P);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addId(Entity $entity): ApiClient
    {
        $this->form[] = new Input('id', 'id', $entity->getId(), [], [Html5::ATTR_TYPE => Input::TYPE_HIDDEN]);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addDescription(Entity $entity): ApiClient
    {
        $input = new Textarea(
            'description',
            'description',
            $entity->getDescription()
        );
        $label = new Label('description', 'admin:apiClientDescription');

        $this->form[] = new FormGroup($input, $label);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addAdminResources(Entity $entity): ApiClient
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
     * @return $this
     */
    protected function addSecret(): ApiClient
    {
        $input = new Input('secret', 'secret', '', [], [Html5::ATTR_READONLY => null]);
        $label = new Label('secret', 'admin:apiClientSecret');

        $container   = new Component(null, [], [], Html5::TAG_DIV);
        $container[] = new Component(
            $this->buttonFactory->createWithIcon(
                'admin:generateSecret',
                'autorenew',
                [],
                [],
                [ButtonWithIcon::INTENT_DANGER, ButtonWithIcon::INTENT_SMALL],
                [
                    Html5::ATTR_ID    => 'generateSecret',
                    'data-positionX'  => 'center',
                    'data-positionY'  => 'top',
                    'data-effect'     => 'fadeInUp',
                    'data-duration'   => '2000',
                    Html5::ATTR_CLASS => 'pmd-alert-toggle',

                ],
                HTML5::TAG_A
            ),
            [],
            [Html5::ATTR_CLASS => 'button-container'],
            Html5::TAG_DIV
        );
        $container[] = new Help(
            'admin:apiClientSecretHelp',
            [Help::INTENT_HIDDEN],
            [Html5::ATTR_ID => 'secretHelp']
        );

        $this->form[] = new FormGroup($input, $label, $container);

        return $this;
    }

    /**
     * @return Label
     */
    protected function createAdminResourceLabel(): Label
    {
        return new Label('admin_resource_ids', 'admin:adminResources');
    }
}
