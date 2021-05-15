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
use Opulence\Sessions\ISession;
use SessionHandlerInterface;

class Authentication extends Session
{
    protected RoutesConfig $routesConfig;

    /**
     * Authentication constructor.
     *
     * @param ISession                $session
     * @param SessionHandlerInterface $sessionHandler
     * @param RoutesConfig            $routesConfig
     */
    public function __construct(ISession $session, SessionHandlerInterface $sessionHandler, RoutesConfig $routesConfig)
    {
        parent::__construct($session, $sessionHandler);

        $this->routesConfig = $routesConfig;
    }

    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->session->has(SessionConstants::USERNAME)) {
            return new RedirectResponse($this->routesConfig->getLoginPath(), ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
        }

        return $next($request);
    }
}
