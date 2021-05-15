<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use Closure;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Http\CsrfTokenChecker;
use Opulence\Http\InvalidCsrfTokenException;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Cookie;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;
use Opulence\Sessions\ISession;

/**
 * Defines the middleware that checks the CSRF token
 */
class CheckCsrfToken implements IMiddleware
{
    /** @var CsrfTokenChecker The CSRF token checker */
    protected CsrfTokenChecker $csrfTokenChecker;

    /** @var ISession The current session */
    protected ISession $session;

    private RoutesConfig $routesConfig;

    /**
     * @param CsrfTokenChecker $csrfTokenChecker The CSRF token checker
     * @param ISession         $session          The current session
     * @param RoutesConfig     $routesConfig
     */
    public function __construct(CsrfTokenChecker $csrfTokenChecker, ISession $session, RoutesConfig $routesConfig)
    {
        $this->csrfTokenChecker = $csrfTokenChecker;
        $this->session          = $session;

        $this->routesConfig = $routesConfig;
    }

    /**
     * @inheritdoc
     * @throws InvalidCsrfTokenException Thrown if the CSRF token is invalid
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isApiRequest($request)) {
            return $next($request);
        }

        if (!$this->csrfTokenChecker->tokenIsValid($request, $this->session)) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        return $this->writeToResponse($next($request));
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isApiRequest(Request $request): bool
    {
        $apiBasePath = $this->routesConfig->getApiBasePath();

        $path = $request->getPath();

        return substr($path, 0, strlen($apiBasePath)) === $apiBasePath;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * Writes data to the response
     *
     * @param Response $response The response to write to
     *
     * @return Response The response with the data written to it
     */
    protected function writeToResponse(Response $response): Response
    {
        $sessionValue = $this->session->get(CsrfTokenChecker::TOKEN_INPUT_NAME);

        $lifetime   = Config::get('sessions', 'xsrfcookie.lifetime');
        $expiration = time() + $lifetime;

        $path     = Config::get('sessions', 'cookie.path');
        $domain   = Config::get('sessions', 'cookie.domain');
        $isSecure = Config::get('sessions', 'cookie.isSecure');

        // Add an XSRF cookie for JavaScript frameworks to use
        $response->getHeaders()->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $sessionValue,
                $expiration,
                $path,
                $domain,
                $isSecure,
                false
            )
        );

        return $response;
    }
}
