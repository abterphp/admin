<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Constant\Routes;
use AbterPhp\Framework\Constant\Session;

class Profile extends User
{
    public function profile()
    {
        $userId = $this->session->get(Session::USER_ID);

        $this->update($userId);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string      $next
     * @param string|null $entityId
     *
     * @return string
     * @throws URLException
     */
    protected function getUrl(string $next, string $entityId = null)
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->urlGenerator;

        $url = $urlGenerator->createFromName(Routes::ROUTE_PROFILE);

        return $url;
    }
}
