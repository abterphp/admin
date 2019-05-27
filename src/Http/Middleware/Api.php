<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Service\Login as LoginService;
use AbterPhp\Framework\Psr7\RequestConverter;
use AbterPhp\Framework\Psr7\ResponseConverter;
use AbterPhp\Framework\Psr7\ResponseFactory;
use Closure;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Middleware\IMiddleware;
use Psr\Log\LoggerInterface;

class Api implements IMiddleware
{
    /** @var ResourceServer */
    protected $server;

    /** @var RequestConverter */
    protected $requestConverter;

    /** @var ResponseFactory */
    protected $responseFactory;

    /** @var ResponseConverter */
    protected $responseConverter;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param LoginService $loginService The session used by the application
     */
    public function __construct(
        ResourceServer $server,
        RequestConverter $requestConverter,
        ResponseFactory $responseFactory,
        ResponseConverter $responseConverter,
        LoggerInterface $logger
    ) {
        $this->server = $server;

        $this->requestConverter  = $requestConverter;
        $this->responseFactory   = $responseFactory;
        $this->responseConverter = $responseConverter;
        $this->logger            = $logger;
    }

    // TODO: Check error response formats
    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        $psr7Request = $this->requestConverter->toPsr($request);

        try {
            $psr7Request = $this->server->validateAuthenticatedRequest($psr7Request);
        } catch (OAuthServerException $e) {
            $psr7Response = $this->responseFactory->create();

            $psr7Response = $e->generateHttpResponse($psr7Response);

            return $this->responseConverter->fromPsr($psr7Response);
        } catch (Exception $e) {
            $psr7Response = $this->responseFactory->create();

            $psr7Response = (new OAuthServerException($e->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($psr7Response);

            return $this->responseConverter->fromPsr($psr7Response);
        }

        // $request = $this->requestConverter->fromPsr($psr7Request);

        return $next($request);
    }
}
