<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

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
    protected $csrfTokenChecker = null;
    /** @var ISession The current session */
    protected $session = null;

    /**
     * @param CsrfTokenChecker $csrfTokenChecker The CSRF token checker
     * @param ISession         $session          The current session
     */
    public function __construct(CsrfTokenChecker $csrfTokenChecker, ISession $session)
    {
        $this->csrfTokenChecker = $csrfTokenChecker;
        $this->session          = $session;
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
        if (!defined('PATH_API')) {
            return false;
        }

        $path = $request->getPath();

        $isApiRequest = substr($path, 0, strlen(PATH_API)) === PATH_API;

        return $isApiRequest;
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
        // Add an XSRF cookie for JavaScript frameworks to use
        $response->getHeaders()->setCookie(
            new Cookie(
                'XSRF-TOKEN',
                $this->session->get(CsrfTokenChecker::TOKEN_INPUT_NAME),
                time() + Config::get('sessions', 'xsrfcookie.lifetime'),
                Config::get('sessions', 'cookie.path'),
                Config::get('sessions', 'cookie.domain'),
                Config::get('sessions', 'cookie.isSecure'),
                false
            )
        );

        return $response;
    }
}
