<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Domain\Entities;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class UserApiKey implements IStringerEntity
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $userId;

    /** @var string */
    protected $description;

    /** @var AdminResource[] */
    protected $adminResources;

    /**
     * ApiKey constructor.
     *
     * @param string          $id
     * @param string          $userId
     * @param string          $description
     * @param AdminResource[] $adminResources
     */
    public function __construct(string $id, string $userId, string $description, array $adminResources = [])
    {
        $this->id             = $id;
        $this->userId         = $userId;
        $this->description    = $description;
        $this->adminResources = $adminResources;
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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     *
     * @return $this
     */
    public function setUserId(string $userId): UserApiKey
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): UserApiKey
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return AdminResource[]
     */
    public function getAdminResources(): array
    {
        return $this->adminResources;
    }

    /**
     * @param AdminResource[] $adminResources
     *
     * @return $this
     */
    public function setAdminResources(array $adminResources): UserApiKey
    {
        $this->adminResources = $adminResources;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDescription();
    }
}
