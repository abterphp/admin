<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Framework\Constant\Session;
use Casbin\Enforcer;
use Casbin\Exceptions\CasbinException;
use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Middleware\ParameterizedMiddleware;
use Opulence\Sessions\ISession;

class Authorization extends ParameterizedMiddleware
{
    public const PATH_403 = '/nope';

    const RESOURCE = 'resource';
    const ROLE     = 'role';

    const RESOURCE_PREFIX = 'admin_resource_';

    /** @var ISession */
    protected $session;

    /** @var Enforcer */
    protected $enforcer;

    /**
     * Authorization constructor.
     *
     * @param ISession $session
     * @param Enforcer $enforcer
     */
    public function __construct(ISession $session, Enforcer $enforcer)
    {
        $this->session  = $session;
        $this->enforcer = $enforcer;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $this->session->get(Session::USERNAME);
        $resource = static::RESOURCE_PREFIX . $this->getParameter(static::RESOURCE);
        $role     = $this->getParameter(static::ROLE);

        try {
            if ($this->enforcer->enforce($username, $resource, $role)) {
                return $next($request);
            }
        } catch (CasbinException $e) {
            return new RedirectResponse(static::PATH_403, ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
        } catch (\Exception $e) {
            throw $e;
        }

        return new RedirectResponse(static::PATH_403, ResponseHeaders::HTTP_TEMPORARY_REDIRECT);
    }
}
