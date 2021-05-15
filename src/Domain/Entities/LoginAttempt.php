<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Domain\Entities;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;

class LoginAttempt implements IStringerEntity
{
    protected string $id;

    protected string $ipHash;

    protected string $username;

    protected ?string $ipAddress;

    /**
     * LoginAttempt constructor.
     *
     * @param string      $id
     * @param string      $ipHash
     * @param string      $username
     * @param string|null $ipAddress
     */
    public function __construct(
        string $id,
        string $ipHash,
        string $username,
        ?string $ipAddress = null
    ) {
        $this->id        = $id;
        $this->ipHash    = $ipHash;
        $this->username  = $username;
        $this->ipAddress = $ipAddress;
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
    public function getIpHash(): string
    {
        return $this->ipHash;
    }

    /**
     * @param string $ipHash
     *
     * @return $this
     */
    public function setIpHash(string $ipHash): LoginAttempt
    {
        $this->ipHash = $ipHash;

        return $this;
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
     */
    public function setUsername(string $username): LoginAttempt
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @param string|null $ipAddress
     */
    public function setIpAddress(?string $ipAddress): LoginAttempt
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getIpHash();
    }

    /**
     * @return array|null
     */
    public function toData(): ?array
    {
        return [
            "id" => $this->getId(),
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
