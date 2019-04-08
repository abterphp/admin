<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Service\Login as LoginService;
use AbterPhp\Website\Constant\Routes;
use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Middleware\IMiddleware;

class Api implements IMiddleware
{
    const REMOTE_ADDR = 'REMOTE_ADDR';

    /** @var LoginService */
    protected $loginService;

    /**
     * @param LoginService $loginService The session used by the application
     */
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->getInput('username');
        $password = $request->getInput('password');

        if (null === $username || null === $password) {
            return new RedirectResponse(Routes::PATH_NOPE, ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
        }

        $username  = (string)$username;
        $password  = (string)$password;
        $ipAddress = (string)$request->getServer()->get(static::REMOTE_ADDR);
        try {
            if (!$this->loginService->isLoginAllowed($username, $ipAddress)) {
                return new RedirectResponse(Routes::PATH_NOPE, ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
            }

            $user = $this->loginService->login($username, $password, $ipAddress);
            if (!$user) {
                return new RedirectResponse(Routes::PATH_NOPE, ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
            }
        } catch (\Exception $e) {
            return new RedirectResponse(Routes::PATH_NOPE, ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
        }

        return $next($request);
    }
}
