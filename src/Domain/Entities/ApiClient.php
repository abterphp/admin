<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Domain\Entities;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class ApiClient implements IStringerEntity
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $userId;

    /** @var string */
    protected $description;

    /** @var string */
    protected $secret;

    /** @var AdminResource[] */
    protected $adminResources;

    /**
     * ApiClient constructor.
     *
     * @param string          $id
     * @param string          $userId
     * @param string          $description
     * @param string          $secret
     * @param AdminResource[] $adminResources
     */
    public function __construct(
        string $id,
        string $userId,
        string $description,
        string $secret,
        array $adminResources = []
    ) {
        $this->id             = $id;
        $this->userId         = $userId;
        $this->description    = $description;
        $this->secret         = $secret;
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
    public function setUserId(string $userId): ApiClient
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
    public function setDescription(string $description): ApiClient
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     *
     * @return $this
     */
    public function setSecret(string $secret): ApiClient
    {
        $this->secret = $secret;

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
    public function setAdminResources(array $adminResources): ApiClient
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

    /**
     * @return array|null
     */
    public function toData(): ?array
    {
        $adminResourceIds = [];
        foreach ($this->getAdminResources() as $adminResource) {
            $adminResourceIds[] = $adminResource->getId();
        }

        return [
            "id"                => $this->getId(),
            "user_id"           => $this->getUserId(),
            "description"       => $this->getDescription(),
            "admin_resource_id" => $adminResourceIds,
        ];
    }

    /**
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode($this->toData());
    }
}
