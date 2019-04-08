<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Events\Listeners;

use AbterPhp\Framework\Events\AuthReady;
use AbterPhp\Admin\Authorization\AdminResourceProvider;
use AbterPhp\Admin\Authorization\UserProvider;

class AuthInitializer
{
    /** @var UserProvider */
    protected $userProvider;

    /**
     * @var AdminResourceProvider
     */
    protected $adminResourceProvider;

    /**
     * AuthRegistrar constructor.
     *
     * @param UserProvider          $userProvider
     * @param AdminResourceProvider $adminResourceProvider
     */
    public function __construct(UserProvider $userProvider, AdminResourceProvider $adminResourceProvider)
    {
        $this->userProvider          = $userProvider;
        $this->adminResourceProvider = $adminResourceProvider;
    }

    /**
     * @param AuthReady $event
     */
    public function handle(AuthReady $event)
    {
        $event->getAdapter()->registerAdapter($this->userProvider);
        $event->getAdapter()->registerAdapter($this->adminResourceProvider);
    }
}
