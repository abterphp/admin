<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Framework\Constant\Session as SessionConstants;
use AbterPhp\Framework\Http\Middleware\Session;
use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;

class Authentication extends Session
{
    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->session->has(SessionConstants::USERNAME)) {
            return new RedirectResponse(RoutesConfig::getLoginPath(), ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
        }

        return $next($request);
    }
}
