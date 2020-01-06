<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\Execute;

use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\UserRepo as GridRepo;
use AbterPhp\Admin\Validation\Factory\ExistingUser as ValidatorFactory;
use AbterPhp\Admin\Validation\Factory\NewUser as NewUserValidatorFactory;
use AbterPhp\Framework\Crypto\Crypto;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Orm\IUnitOfWork;
use Opulence\Validation\IValidator;

class User extends RepoServiceAbstract
{
    /** @var Crypto */
    private $crypto;

    /** @var NewUserValidatorFactory */
    private $newUserValidatorFactory;

    /**
     * User constructor.
     *
     * @param GridRepo                $repo
     * @param ValidatorFactory        $validatorFactory
     * @param IUnitOfWork             $unitOfWork
     * @param IEventDispatcher        $eventDispatcher
     * @param Crypto                  $crypto
     * @param NewUserValidatorFactory $newUserValidatorFactory
     */
    public function __construct(
        GridRepo $repo,
        ValidatorFactory $validatorFactory,
        IUnitOfWork $unitOfWork,
        IEventDispatcher $eventDispatcher,
        Crypto $crypto,
        NewUserValidatorFactory $newUserValidatorFactory
    ) {
        parent::__construct(
            $repo,
            $validatorFactory,
            $unitOfWork,
            $eventDispatcher
        );

        $this->crypto                  = $crypto;
        $this->newUserValidatorFactory = $newUserValidatorFactory;
    }

    /**
     * @param int $additionalData
     *
     * @return IValidator
     */
    protected function getValidator(int $additionalData): IValidator
    {
        if ($additionalData == static::CREATE) {
            $this->validator = $this->newUserValidatorFactory->createValidator();
        }

        if ($this->validator) {
            return $this->validator;
        }

        $this->validator = $this->validatorFactory->createValidator();

        return $this->validator;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    public function createEntity(string $entityId): IStringerEntity
    {
        $userLanguage = new UserLanguage(
            '',
            '',
            ''
        );
        $entity       = new Entity(
            $entityId,
            '',
            '',
            '',
            false,
            false,
            $userLanguage
        );

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
        assert($entity instanceof Entity, new \InvalidArgumentException('Invalid entity'));

        $username          = isset($postData['username']) ? (string)$postData['username'] : '';
        $email             = isset($postData['email']) ? (string)$postData['email'] : '';
        $password          = isset($postData['password']) ? (string)$postData['password'] : '';
        $isGravatarAllowed = isset($postData['is_gravatar_allowed']) ? (bool)$postData['is_gravatar_allowed'] : false;
        $canLogin          = isset($postData['can_login']) ? (bool)$postData['can_login'] : false;
        $userLanguage      = $this->createUserLanguage($postData);
        $userGroups        = $this->createUserGroups($postData);

        $entity->setUsername($username)
            ->setEmail($email)
            ->setIsGravatarAllowed($isGravatarAllowed)
            ->setCanLogin($canLogin)
            ->setUserLanguage($userLanguage)
            ->setUserGroups($userGroups);

        if ($password) {
            $entity->setPassword($this->crypto->hashCrypt($password));
        }

        return $entity;
    }

    /**
     * @param array $postData
     *
     * @return UserLanguage
     */
    protected function createUserLanguage(array $postData): UserLanguage
    {
        $userLanguageId = isset($postData['user_language_id']) ? (string)$postData['user_language_id'] : '';

        return new UserLanguage($userLanguageId, '', '');
    }

    /**
     * @param array $postData
     *
     * @return array
     */
    protected function createUserGroups(array $postData): array
    {
        $userGroups = [];
        if (!empty($postData['user_group_ids'])) {
            foreach ($postData['user_group_ids'] as $userGroupId) {
                $userGroups[] = new UserGroup(
                    (string)$userGroupId,
                    '',
                    ''
                );
            }
        }

        return $userGroups;
    }
}
