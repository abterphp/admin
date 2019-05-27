<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Oauth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class Client implements ClientEntityInterface
{
    /** @var string */
    protected $identifier;

    /** @var string */
    protected $name;

    /** @var string */
    protected $redirectUri;

    /**
     * Client constructor.
     *
     * @param string $identifier
     * @param string $name
     * @param string $redirectUri
     */
    public function __construct(string $identifier, string $name, string $redirectUri)
    {
        $this->identifier  = $identifier;
        $this->name        = $name;
        $this->redirectUri = $redirectUri;
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

    /**
     * Get the client's name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the registered redirect URI (as a string).
     *
     * Alternatively return an indexed array of redirect URIs.
     *
     * @return string|string[]
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }
}
