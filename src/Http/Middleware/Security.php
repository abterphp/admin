<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Exception\Security as SecurityException;
use Closure;
use Opulence\Cache\ICacheBridge;
use Opulence\Environments\Environment;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;

class Security implements IMiddleware
{
    const KEY = 'abteradmin:security';

    /** @var ICacheBridge */
    protected $cacheBridge;

    /**
     * Security constructor.
     *
     * @param ICacheBridge $cacheBridge
     */
    public function __construct(ICacheBridge $cacheBridge)
    {
        $this->cacheBridge = $cacheBridge;
    }

    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        if (getenv(\AbterPhp\Framework\Constant\Env::ENV_NAME) !== Environment::PRODUCTION) {
            return $next($request);
        }

        // phpcs:disable Generic.CodeAnalysis.EmptyStatement
        try {
            if ($this->cacheBridge->has(static::KEY)) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // It's always safe to check the security checks, it just makes the response slightly slower
        }
        // phpcs:enable Generic.CodeAnalysis.EmptyStatement

        $this->checkRoutes();
        $this->checkApi();

        $this->cacheBridge->set(static::KEY, true, PHP_INT_MAX);

        return $next($request);
    }

    private function checkRoutes()
    {
        if (getenv(ADMIN_LOGIN_PATH) === '/admin-iddqd') {
            throw new SecurityException('Invalid ADMIN_LOGIN_PATH environment variable.');
        }

        if (getenv(ADMIN_BASE_PATH) === '/login-iddqd') {
            throw new SecurityException('Invalid ADMIN_BASE_PATH environment variable.');
        }

        if (getenv(API_BASE_PATH) === '/api-iddqd') {
            throw new SecurityException('Invalid ADMIN_BASE_PATH environment variable.');
        }
    }

    private function checkApi()
    {
        if (getenv(Env::OAUTH_PRIVATE_KEY_PASSWORD) === 'CuDU2M9FRD8ckRxj9dhB82f6VjMs4EMf') {
            throw new SecurityException('Invalid OAUTH_PRIVATE_KEY_PASSWORD environment variable.');
        }
    }
}
