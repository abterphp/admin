<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Domain\Entities;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class User implements IStringerEntity
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $username;

    /** @var string */
    protected $email;

    /** @var string */
    protected $password;

    /** @var bool */
    protected $canLogin;

    /** @var bool */
    protected $isGravatarAllowed;

    /** @var UserLanguage */
    protected $userLanguage;

    /** @var UserGroup[] */
    protected $userGroups;

    /**
     * User constructor.
     *
     * @param string       $id
     * @param string       $username
     * @param string       $email
     * @param string       $password
     * @param bool         $canLogin
     * @param bool         $isGravatarAllowed
     * @param UserLanguage $userLanguage
     * @param UserGroup[]  $userGroups
     */
    public function __construct(
        string $id,
        string $username,
        string $email,
        string $password,
        bool $canLogin,
        bool $isGravatarAllowed,
        UserLanguage $userLanguage,
        array $userGroups = []
    ) {
        $this->id                = $id;
        $this->username          = $username;
        $this->email             = $email;
        $this->password          = $password;
        $this->canLogin          = $canLogin;
        $this->isGravatarAllowed = $isGravatarAllowed;
        $this->userLanguage      = $userLanguage;

        $this->setUserGroups($userGroups);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return bool
     */
    public function canLogin(): bool
    {
        return $this->canLogin;
    }

    /**
     * @param bool $canLogin
     *
     * @return $this
     */
    public function setCanLogin(bool $canLogin): User
    {
        $this->canLogin = $canLogin;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGravatarAllowed(): bool
    {
        return $this->isGravatarAllowed;
    }

    /**
     * @param bool $isGravatarAllowed
     *
     * @return $this
     */
    public function setIsGravatarAllowed(bool $isGravatarAllowed): User
    {
        $this->isGravatarAllowed = $isGravatarAllowed;

        return $this;
    }

    /**
     * @return UserLanguage
     */
    public function getUserLanguage(): UserLanguage
    {
        return $this->userLanguage;
    }

    /**
     * @param UserLanguage $userLanguage
     *
     * @return $this
     */
    public function setUserLanguage(UserLanguage $userLanguage): User
    {
        $this->userLanguage = $userLanguage;

        return $this;
    }

    /**
     * @return UserGroup[]
     */
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @param UserGroup[] $userGroups
     *
     * @return $this
     */
    public function setUserGroups(array $userGroups): User
    {
        foreach ($userGroups as $userGroup) {
            if (!($userGroup instanceof UserGroup)) {
                throw new \InvalidArgumentException();
            }
        }

        $this->userGroups = $userGroups;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
    }

    /**
     * @return string
     */
    public function toJSON(): string
    {
        $userGroupIds = [];
        foreach ($this->getUserGroups() as $userGroup) {
            $userGroupIds[] = $userGroup->getId();
        }

        $userLanguageId = $this->getUserLanguage()->getId();

        return json_encode(
            [
                "id"                  => $this->getId(),
                "username"            => $this->getUsername(),
                "email"               => $this->getEmail(),
                "can_login"           => $this->canLogin(),
                "is_gravatar_allowed" => $this->isGravatarAllowed(),
                "user_group_ids"      => $userGroupIds,
                "user_language_id"    => $userLanguageId,
            ]
        );
    }
}
