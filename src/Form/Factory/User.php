<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Form\Factory;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\UserGroupRepo;
use AbterPhp\Admin\Orm\UserLanguageRepo;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Form\Container\CheckboxGroup;
use AbterPhp\Framework\Form\Container\FormGroup;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Element\MultiSelect;
use AbterPhp\Framework\Form\Element\Select;
use AbterPhp\Framework\Form\Extra\Help;
use AbterPhp\Framework\Form\IForm;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\I18n\ITranslator;
use Opulence\Orm\IEntity;
use Opulence\Orm\OrmException;
use Opulence\Sessions\ISession;

class User extends Base
{
    protected UserGroupRepo $userGroupRepo;

    protected UserLanguageRepo $userLanguageRepo;

    /**
     * User constructor.
     *
     * @param ISession         $session
     * @param ITranslator      $translator
     * @param UserGroupRepo    $userGroupRepo
     * @param UserLanguageRepo $userLanguageRepo
     */
    public function __construct(
        ISession $session,
        ITranslator $translator,
        UserGroupRepo $userGroupRepo,
        UserLanguageRepo $userLanguageRepo
    ) {
        parent::__construct($session, $translator);

        $this->userGroupRepo    = $userGroupRepo;
        $this->userLanguageRepo = $userLanguageRepo;
    }

    /**
     * @param string       $action
     * @param string       $method
     * @param string       $showUrl
     * @param IEntity|null $entity
     *
     * @return IForm
     * @throws OrmException
     */
    public function create(string $action, string $method, string $showUrl, ?IEntity $entity = null): IForm
    {
        assert($entity instanceof Entity, new \InvalidArgumentException());

        $this->createForm($action, $method)
            ->addDefaultElements()
            ->addJsOnly()
            ->addUsername($entity)
            ->addEmail($entity)
            ->addPassword()
            ->addPasswordConfirmed()
            ->addRawPassword($entity)
            ->addRawPasswordConfirmed($entity)
            ->addCanLogin($entity)
            ->addIsGravatarAllowed($entity)
            ->addUserGroups($entity)
            ->addUserLanguages($entity)
            ->addDefaultButtons($showUrl);

        $form = $this->form;

        $this->form = null;

        return $form;
    }

    /**
     * @return $this
     */
    protected function addJsOnly(): User
    {
        $content    = sprintf(
            '<i class="material-icons">warning</i>&nbsp;%s',
            $this->translator->translate('admin:jsOnly')
        );
        $attributes = Attributes::fromArray([Html5::ATTR_CLASS => 'only-js-form-warning']);

        $this->form[] = new Tag($content, [], $attributes, Html5::TAG_P);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addUsername(Entity $entity): User
    {
        $attributes = Attributes::fromArray([Html5::ATTR_NAME => [Input::AUTOCOMPLETE_OFF]]);
        $input      = new Input('username', 'username', $entity->getUsername(), [], $attributes);
        $label      = new Label('username', 'admin:userUsername');

        $attributes   = Attributes::fromArray([Html5::ATTR_CLASS => FormGroup::CLASS_REQUIRED]);
        $this->form[] = new FormGroup($input, $label, null, [], $attributes);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addEmail(Entity $entity): User
    {
        $input = new Input(
            'email',
            'email',
            $entity->getEmail(),
            [],
            Attributes::fromArray(
                [
                    Html5::ATTR_AUTOCOMPLETE => [Input::AUTOCOMPLETE_OFF],
                    Html5::ATTR_TYPE         => Input::TYPE_EMAIL,
                ]
            )
        );
        $label = new Label('email', 'admin:userEmail');

        $attributes   = Attributes::fromArray([Html5::ATTR_CLASS => FormGroup::CLASS_REQUIRED]);
        $this->form[] = new FormGroup($input, $label, null, [], $attributes);

        return $this;
    }

    /**
     * @return $this
     */
    protected function addPassword(): User
    {
        $this->form[] = new Input(
            'password',
            'password',
            '',
            [],
            Attributes::fromArray([Html5::ATTR_TYPE => [Input::TYPE_HIDDEN]])
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addPasswordConfirmed(): User
    {
        $this->form[] = new Input(
            'password_confirmed',
            'password_confirmed',
            '',
            [],
            Attributes::fromArray([Html5::ATTR_TYPE => [Input::TYPE_HIDDEN]])
        );

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addRawPassword(Entity $entity): User
    {
        $input = new Input(
            'raw_password',
            'raw_password',
            '',
            [],
            Attributes::fromArray(
                [
                    Html5::ATTR_NAME => [Input::AUTOCOMPLETE_OFF],
                    Html5::ATTR_TYPE => [Input::TYPE_PASSWORD],
                ]
            )
        );
        $label = new Label('raw_password', 'admin:userPassword');

        $class = $entity->getId() ? '' : FormGroup::CLASS_REQUIRED;

        $attributes   = Attributes::fromArray([Html5::ATTR_CLASS => $class]);
        $this->form[] = new FormGroup($input, $label, null, [], $attributes);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addRawPasswordConfirmed(Entity $entity): User
    {
        $input = new Input(
            'raw_password_confirmed',
            'raw_password_confirmed',
            '',
            [],
            Attributes::fromArray(
                [
                    Html5::ATTR_NAME => [Input::AUTOCOMPLETE_OFF],
                    Html5::ATTR_TYPE => [Input::TYPE_PASSWORD],
                ]
            )
        );
        $label = new Label('raw_password_confirmed', 'admin:userConfirmPassword');

        $class = $entity->getId() ? '' : FormGroup::CLASS_REQUIRED;

        $attributes   = Attributes::fromArray([Html5::ATTR_CLASS => $class]);
        $this->form[] = new FormGroup($input, $label, null, [], $attributes);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addCanLogin(Entity $entity): User
    {
        $attributes = Attributes::fromArray([Html5::ATTR_TYPE => [Input::TYPE_CHECKBOX]]);
        if ($entity->canLogin()) {
            $attributes = Attributes::addItem($attributes, Html5::ATTR_CHECKED);
        }
        $input = new Input(
            'can_login',
            'can_login',
            '1',
            [],
            $attributes
        );
        $label = new Label('can_login', 'admin:userCanLogin');
        $help  = new Help('admin:userCanLoginHelp');

        $this->form[] = new CheckboxGroup($input, $label, $help);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     */
    protected function addIsGravatarAllowed(Entity $entity): User
    {
        $attributes = Attributes::fromArray([Html5::ATTR_TYPE => [Input::TYPE_CHECKBOX]]);
        if ($entity->isGravatarAllowed()) {
            $attributes = Attributes::addItem($attributes, Html5::ATTR_CHECKED);
        }
        $input = new Input(
            'is_gravatar_allowed',
            'is_gravatar_allowed',
            '1',
            [],
            $attributes
        );
        $label = new Label(
            'is_gravatar_allowed',
            'admin:userIsGravatarAllowed'
        );
        $help  = new Help('admin:userIsGravatarAllowedHelp');

        $this->form[] = new CheckboxGroup($input, $label, $help);

        return $this;
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     * @throws OrmException
     */
    protected function addUserGroups(Entity $entity): User
    {
        $allUserGroups = $this->getAllUserGroups();

        $userGroupIds = [];
        foreach ($entity->getUserGroups() as $userGroup) {
            $userGroupIds[] = $userGroup->getId();
        }

        $options = $this->createUserGroupOptions($allUserGroups, $userGroupIds);

        $this->form[] = new FormGroup(
            $this->createUserGroupSelect($options),
            $this->createUserGroupLabel()
        );

        return $this;
    }

    /**
     * @return UserGroup[]
     * @throws OrmException
     */
    protected function getAllUserGroups(): array
    {
        return $this->userGroupRepo->getAll();
    }

    /**
     * @param UserGroup[] $allUserGroups
     * @param UserGroup[] $userGroupIds
     *
     * @return array
     */
    protected function createUserGroupOptions(array $allUserGroups, array $userGroupIds): array
    {
        $options = [];
        foreach ($allUserGroups as $userGroup) {
            $isSelected = in_array($userGroup->getId(), $userGroupIds, true);
            $options[]  = new Option($userGroup->getId(), $userGroup->getName(), $isSelected);
        }

        return $options;
    }

    /**
     * @param Option[] $options
     *
     * @return Select
     */
    protected function createUserGroupSelect(array $options): Select
    {
        $size       = $this->getMultiSelectSize(
            count($options),
            static::MULTISELECT_MIN_SIZE,
            static::MULTISELECT_MAX_SIZE
        );
        $attributes = Attributes::fromArray([Html5::ATTR_SIZE => [(string)$size]]);

        $select = new MultiSelect('user_group_ids', 'user_group_ids[]', [], $attributes);

        foreach ($options as $option) {
            $select[] = $option;
        }

        return $select;
    }

    /**
     * @return Label
     */
    protected function createUserGroupLabel(): Label
    {
        return new Label('user_group_ids', 'admin:userGroups');
    }

    /**
     * @param Entity $entity
     *
     * @return $this
     * @throws OrmException
     */
    protected function addUserLanguages(Entity $entity): User
    {
        $allUserGroups = $this->getAllUserLanguages();
        $userGroupId   = $entity->getUserLanguage()->getId();

        $options = $this->createUserLanguageOptions($allUserGroups, $userGroupId);

        $attributes = Attributes::fromArray([Html5::ATTR_CLASS => FormGroup::CLASS_REQUIRED]);
        $this->form[] = new FormGroup(
            $this->createUserLanguageSelect($options),
            $this->createUserLanguageLabel(),
            null,
            [],
            $attributes
        );

        return $this;
    }

    /**
     * @return UserLanguage[]
     * @throws OrmException
     */
    protected function getAllUserLanguages(): array
    {
        return $this->userLanguageRepo->getAll();
    }

    /**
     * @param UserLanguage[] $allUserLanguages
     * @param string         $userLanguageId
     *
     * @return array
     */
    protected function createUserLanguageOptions(array $allUserLanguages, string $userLanguageId): array
    {
        $options = [];
        foreach ($allUserLanguages as $userLanguage) {
            $isSelected = $userLanguageId === $userLanguage->getId();
            $options[]  = new Option($userLanguage->getId(), $userLanguage->getName(), $isSelected);
        }

        return $options;
    }

    /**
     * @param Option[] $options
     *
     * @return Select
     */
    protected function createUserLanguageSelect(array $options): Select
    {
        $size       = $this->getMultiSelectSize(
            count($options),
            static::MULTISELECT_MIN_SIZE,
            static::MULTISELECT_MAX_SIZE
        );
        $attributes = Attributes::fromArray([Html5::ATTR_SIZE => [(string)$size]]);

        $select = new MultiSelect('user_language_id', 'user_language_id', [], $attributes);

        foreach ($options as $option) {
            $select[] = $option;
        }

        return $select;
    }

    /**
     * @return Label
     */
    protected function createUserLanguageLabel(): Label
    {
        return new Label('user_language_id', 'admin:userLanguages');
    }
}
