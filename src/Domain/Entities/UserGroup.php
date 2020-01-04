<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Domain\Entities;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class UserGroup implements IStringerEntity
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var AdminResource[] */
    protected $adminResources;

    /**
     * UserGroup constructor.
     *
     * @param string $id
     * @param string $identifier
     * @param string $name
     * @param array  $adminResources
     */
    public function __construct(
        string $id,
        string $identifier,
        string $name,
        array $adminResources = []
    ) {
        $this->id         = $id;
        $this->identifier = $identifier;
        $this->name       = $name;

        $this->setAdminResources($adminResources);
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
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     *
     * @return $this
     */
    public function setIdentifier(string $identifier): UserGroup
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): UserGroup
    {
        $this->name = $name;

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
    public function setAdminResources(array $adminResources): UserGroup
    {
        foreach ($adminResources as $adminResource) {
            assert($adminResource instanceof AdminResource, new \InvalidArgumentException());
        }

        $this->adminResources = $adminResources;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
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
            "id"                 => $this->getId(),
            "identifier"         => $this->getIdentifier(),
            "name"               => $this->getName(),
            "admin_resource_ids" => $adminResourceIds,
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
