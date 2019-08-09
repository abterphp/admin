<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Admin\Psr7\RequestConverter;
use AbterPhp\Admin\Psr7\ResponseConverter;
use AbterPhp\Admin\Psr7\ResponseFactory;
use AbterPhp\Framework\Config\EnvReader;
use AbterPhp\Framework\Constant\Env;
use Closure;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;
use Opulence\Orm\OrmException;
use Opulence\Routing\Middleware\IMiddleware;
use Psr\Http\Message\ServerRequestInterface;
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

    /** @var UserRepo */
    protected $userRepo;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $problemBaseUrl;

    /**
     * Api constructor.
     *
     * @param ResourceServer    $server
     * @param RequestConverter  $requestConverter
     * @param ResponseFactory   $responseFactory
     * @param ResponseConverter $responseConverter
     * @param UserRepo          $userRepo
     * @param LoggerInterface   $logger
     * @param EnvReader         $envReader
     */
    public function __construct(
        ResourceServer $server,
        RequestConverter $requestConverter,
        ResponseFactory $responseFactory,
        ResponseConverter $responseConverter,
        UserRepo $userRepo,
        LoggerInterface $logger,
        EnvReader $envReader
    ) {
        $this->server = $server;

        $this->requestConverter  = $requestConverter;
        $this->responseFactory   = $responseFactory;
        $this->responseConverter = $responseConverter;
        $this->userRepo          = $userRepo;
        $this->logger            = $logger;
        $this->problemBaseUrl    = $envReader->get(Env::API_PROBLEM_BASE_URL);
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

        try {
            $user = $this->getUserByClientId($psr7Request);
            if (null === $user) {
                throw new Exception('Unexpected user retrieval error');
            }
        } catch (Exception $e) {
            return $this->createResponse(new OAuthServerException($e->getMessage(), 0, 'unknown_error', 500));
        }

        // This is a workaround as Opulence request doesn't have a straight-forward way of storing internal data
        $headers = $request->getHeaders();

        $headers['xxx-user-id']       = $user->getId();
        $headers['xxx-user-username'] = $user->getUsername();

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

    /**
     * @param ServerRequestInterface $psr7Request
     *
     * @return User|null
     * @throws OrmException
     */
    protected function getUserByClientId(ServerRequestInterface $psr7Request): ?User
    {
        $userId = $psr7Request->getAttribute('oauth_user_id');
        if ($userId) {
            $user = $this->userRepo->getById($userId);

            if ($user instanceof User) {
                return $user;
            }
        }

        $clientId = $psr7Request->getAttribute('oauth_client_id');

        $user = $this->userRepo->getByClientId($clientId);
        if ($user instanceof User) {
            return $user;
        }

        return null;
    }
}
