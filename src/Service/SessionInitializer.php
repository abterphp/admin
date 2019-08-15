<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service;

use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Framework\Constant\Session;
use Opulence\Sessions\ISession;

class SessionInitializer
{
    /** @var ISession */
    protected $session;

    /**
     * SessionInitializer constructor.
     *
     * @param ISession $session
     */
    public function __construct(ISession $session)
    {
        $this->session = $session;
    }

    /**
     * @param User $user
     */
    public function initialize(User $user)
    {
        if ($this->session->has(Session::USER_ID) && $this->session->get(Session::USER_ID) === $user->getId()) {
            return;
        }

        $this->session->set(Session::IS_LOGGED_IN, true);
        $this->session->set(Session::USER_ID, $user->getId());
        $this->session->set(Session::USERNAME, $user->getUsername());
        $this->session->set(Session::EMAIL, $user->getEmail());
        $this->session->set(Session::IS_GRAVATAR_ALLOWED, $user->isGravatarAllowed());
        $this->session->set(Session::LANGUAGE_IDENTIFIER, $user->getUserLanguage()->getIdentifier());
    }
}
