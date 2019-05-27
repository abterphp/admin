<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Events\Listeners;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserGroup;
use AbterPhp\Framework\Authorization\CacheManager;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Events\EntityChange;
use Opulence\Sessions\ISession;

class AuthInvalidator
{
    /** @var CacheManager */
    protected $cacheManager;

    /** @var ISession */
    protected $session;

    /**
     * AuthInvalidator constructor.
     *
     * @param CacheManager $cacheManager
     * @param ISession     $session
     */
    public function __construct(CacheManager $cacheManager, ISession $session)
    {
        $this->cacheManager = $cacheManager;

        $this->session = $session;
    }

    /**
     * @param EntityChange $event
     */
    public function handle(EntityChange $event)
    {
        switch ($event->getEntityName()) {
            case AdminResource::class:
            case User::class:
            case UserGroup::class:
                try {
                    $this->cacheManager->clearAll();
                } catch (\Exception $e) {
                    // Exception ignored on purpose. Request is likely an API call...
                    $e;
                }
                break;
        }

        if ($event->getEntityName() == User::class && $event->getEntityId() == $this->session->get(Session::USER_ID)) {
            try {
                $this->session->flush();
            } catch (\Exception $e) {
                // Exception ignored on purpose. Request is likely an API call...
                $e;
            }
        }
    }
}
