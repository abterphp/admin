<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Service\Login as LoginService;
use AbterPhp\Framework\Config\Provider as ConfigProvider;
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

    /** @var string */
    protected $problemBaseUrl;

    /**
     * @param LoginService $loginService The session used by the application
     */
    public function __construct(
        ResourceServer $server,
        RequestConverter $requestConverter,
        ResponseFactory $responseFactory,
        ResponseConverter $responseConverter,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->server = $server;

        $this->requestConverter  = $requestConverter;
        $this->responseFactory   = $responseFactory;
        $this->responseConverter = $responseConverter;
        $this->logger            = $logger;
        $this->problemBaseUrl    = $configProvider->getProblemBaseUrl();
    }

    // TODO: Check error response formats
    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        $psr7Request = $this->requestConverter->toPsr($request);

        try {
            $psr7Request = $this->server->validateAuthenticatedRequest($psr7Request);
        } catch (OAuthServerException $e) {
            return $this->createResponse($e);
        } catch (Exception $e) {
            return $this->createResponse(new OAuthServerException($e->getMessage(), 0, 'unknown_error', 500));
        }

        // $request = $this->requestConverter->fromPsr($psr7Request);

        return $next($request);
    }

    /**
     * @param OAuthServerException $e
     *
     * @return Response
     */
    protected function createResponse(OAuthServerException $e): Response
    {
        $status  = $e->getHttpStatusCode();
        $content = [
            'type'   => sprintf('%srequest-authentication-failure', $this->problemBaseUrl),
            'title'  => 'Access Denied',
            'status' => $status,
            'detail' => $e->getMessage(),
        ];

        $response = new Response();
        $response->setStatusCode($status);
        $response->setContent(json_encode($content));

        return $response;
    }
}
