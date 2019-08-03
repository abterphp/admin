<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Domain\Entities;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class AdminResource implements IStringerEntity
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $identifier;

    /**
     * Block constructor.
     *
     * @param string $id
     * @param string $identifier
     */
    public function __construct(string $id, string $identifier)
    {
        $this->id         = $id;
        $this->identifier = $identifier;
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
    public function setIdentifier(string $identifier): AdminResource
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getIdentifier();
    }

    /**
     * @return string
     */
    public function toJSON(): string
    {
        return json_encode(
            [
                "id"         => $this->getId(),
                "identifier" => $this->getIdentifier(),
            ]
        );
    }
}
