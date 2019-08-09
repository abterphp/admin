<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\Constant\Session;
use Opulence\Routing\Urls\URLException;
use Opulence\Routing\Urls\UrlGenerator; // @phan-suppress-current-line PhanUnreferencedUseNormal
use Opulence\Sessions\ISession; // @phan-suppress-current-line PhanUnreferencedUseNormal

trait UrlTrait
{
    /**
     * @return string
     * @throws URLException
     */
    protected function getShowUrl(): string
    {
        /** @var ISession $session */
        $session = $this->session; // @phan-suppress-current-line PhanUndeclaredProperty

        if ($session->has(Session::LAST_GRID_URL)) {
            return (string)$session->get(Session::LAST_GRID_URL);
        }

        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->urlGenerator; // @phan-suppress-current-line PhanUndeclaredProperty

        // @phan-suppress-next-line PhanUndeclaredConstant
        $url = $urlGenerator->createFromName(strtolower(static::ENTITY_PLURAL));

        return $url;
    }

    /**
     * @param string $id
     *
     * @return string
     * @throws URLException
     */
    protected function getEditUrl(string $id): string
    {
        // @phan-suppress-next-line PhanUndeclaredConstant
        $routeName = sprintf(static::URL_EDIT, strtolower(static::ENTITY_PLURAL));

        // @phan-suppress-next-line PhanUndeclaredProperty
        $url = $this->urlGenerator->createFromName($routeName, [$id]);

        return $url;
    }
}
