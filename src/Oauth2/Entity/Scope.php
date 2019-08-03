<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class Scope implements ScopeEntityInterface
{
    /** @var string */
    protected $identifier;

    /**
     * Scope constructor.
     *
     * @param string $identifier
     */
    public function __construct(string $identifier)
    {
        $this->identifier  = $identifier;
    }

    /**
     * Get the client's identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function jsonSerialize()
    {
        return $this->identifier;
    }
}
