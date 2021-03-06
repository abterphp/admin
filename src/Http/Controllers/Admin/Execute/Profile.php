<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Constant\Route;
use AbterPhp\Framework\Constant\Session;
use Opulence\Routing\Urls\UrlException;

class Profile extends User
{
    /**
     * @throws URLException
     */
    public function profile()
    {
        $userId = (string)$this->session->get(Session::USER_ID);

        $this->update($userId);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string      $next
     * @param string|null $entityId
     *
     * @return string
     * @throws \Opulence\Routing\Urls\URLException
     */
    protected function getUrl(string $next, string $entityId = null): string
    {
        return $this->urlGenerator->createFromName(Route::PROFILE_EDIT);
    }
}
