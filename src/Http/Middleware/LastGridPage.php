<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Helper\Url;
use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;
use Opulence\Sessions\ISession;

class LastGridPage implements IMiddleware
{
    /** @var ISession */
    protected ISession $session;

    /**
     * LastGridPage constructor.
     *
     * @param ISession $session
     */
    public function __construct(ISession $session)
    {
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->getStatusCode() >= 400 || !$this->session->has(Session::USER_ID)) {
            return $response;
        }

        $path = sprintf(
            '%s%s',
            $request->getPath(),
            Url::toQuery($request->getQuery()->getAll())
        );

        $this->session->set(Session::LAST_GRID_URL, $path);

        return $response;
    }
}
