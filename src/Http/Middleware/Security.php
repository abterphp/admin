<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
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

    const TEST_LOGIN_PATH                  = '/login-iddqd';
    const TEST_ADMIN_BASE_PATH             = '/admin-iddqd';
    const TEST_API_BASE_PATH               = '/api-iddqd';
    const TEST_OAUTH2_PRIVATE_KEY_PASSWORD = 'CuDU2M9FRD8ckRxj9dhB82f6VjMs4EMf';

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
        if (Environment::getVar(Env::ENV_NAME, Environment::PRODUCTION) !== Environment::PRODUCTION) {
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
        $this->checkApi($request);

        $this->cacheBridge->set(static::KEY, true, PHP_INT_MAX);

        return $next($request);
    }

    private function checkRoutes()
    {
        if (RoutesConfig::getLoginPath() === static::TEST_LOGIN_PATH) {
            throw new SecurityException('Invalid ADMIN_LOGIN_PATH environment variable.');
        }

        if (RoutesConfig::getAdminBasePath() === static::TEST_ADMIN_BASE_PATH) {
            throw new SecurityException('Invalid ADMIN_BASE_PATH environment variable.');
        }

        if (RoutesConfig::getApiBasePath() === static::TEST_API_BASE_PATH) {
            throw new SecurityException('Invalid ADMIN_BASE_PATH environment variable.');
        }
    }

    /**
     * @param Request $request
     */
    private function checkApi(Request $request)
    {
        $env = $request->getEnv();

        if (empty($env[Env::OAUTH2_PRIVATE_KEY_PASSWORD])) {
            throw new SecurityException('Invalid OAUTH_PRIVATE_KEY_PASSWORD environment variable.');
        }
        if ($env[Env::OAUTH2_PRIVATE_KEY_PASSWORD] === static::TEST_OAUTH2_PRIVATE_KEY_PASSWORD) {
            throw new SecurityException('Invalid OAUTH_PRIVATE_KEY_PASSWORD environment variable.');
        }
    }
}
