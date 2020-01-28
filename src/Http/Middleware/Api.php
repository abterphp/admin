<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Constant\Env;
use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Admin\Psr7\RequestConverter;
use AbterPhp\Framework\Config\EnvReader;
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
    const ATTRIBUTE_USER_ID   = 'oauth_user_id';
    const ATTRIBUTE_CLIENT_ID = 'oauth_client_id';

    const HEADER_USER_ID       = 'xxx-user-id';
    const HEADER_USER_USERNAME = 'xxx-user-username';

    /** @var ResourceServer */
    protected $resourceServer;

    /** @var RequestConverter */
    protected $requestConverter;

    /** @var UserRepo */
    protected $userRepo;

    /** @var LoggerInterface */
    protected $logger;

    /** @var string */
    protected $problemBaseUrl;

    /**
     * Api constructor.
     *
     * @param ResourceServer   $resourceServer
     * @param RequestConverter $requestConverter
     * @param UserRepo         $userRepo
     * @param LoggerInterface  $logger
     * @param EnvReader        $envReader
     */
    public function __construct(
        ResourceServer $resourceServer,
        RequestConverter $requestConverter,
        UserRepo $userRepo,
        LoggerInterface $logger,
        EnvReader $envReader
    ) {
        $this->resourceServer = $resourceServer;

        $this->requestConverter = $requestConverter;
        $this->userRepo         = $userRepo;
        $this->logger           = $logger;
        $this->problemBaseUrl   = $envReader->get(Env::API_PROBLEM_BASE_URL);
    }

    // TODO: Check error response formats
    // $next consists of the next middleware in the pipeline
    public function handle(Request $request, Closure $next): Response
    {
        $psr7Request = $this->requestConverter->toPsr($request);

        try {
            $psr7Request = $this->resourceServer->validateAuthenticatedRequest($psr7Request);
        } catch (OAuthServerException $e) {
            return $this->createResponse($e);
        } catch (Exception $e) {
            return $this->createResponse(new OAuthServerException($e->getMessage(), 0, 'unknown_error', 500));
        }

        try {
            $user = $this->getUser($psr7Request);
            if (null === $user) {
                throw new Exception('Unexpected user retrieval error');
            }
        } catch (Exception $e) {
            return $this->createResponse(new OAuthServerException($e->getMessage(), 0, 'unknown_error', 500));
        }

        // This is a workaround as Opulence request doesn't have a straight-forward way of storing internal data
        $headers = $request->getHeaders();

        $headers[static::HEADER_USER_ID]       = $user->getId();
        $headers[static::HEADER_USER_USERNAME] = $user->getUsername();

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
    protected function getUser(ServerRequestInterface $psr7Request): ?User
    {
        $user = $this->getUserByUserId($psr7Request);
        if ($user) {
            return $user;
        }

        return $this->getUserByClientId($psr7Request);
    }

    /**
     * @param ServerRequestInterface $psr7Request
     *
     * @return User|null
     * @throws OrmException
     */
    protected function getUserByUserId(ServerRequestInterface $psr7Request): ?User
    {
        $userId = $psr7Request->getAttribute(static::ATTRIBUTE_USER_ID);
        if (!$userId) {
            return null;
        }

        $user = $this->userRepo->getById($userId);

        return $user;
    }

    /**
     * @param ServerRequestInterface $psr7Request
     *
     * @return User|null
     * @throws OrmException
     */
    protected function getUserByClientId(ServerRequestInterface $psr7Request): ?User
    {
        $clientId = $psr7Request->getAttribute(static::ATTRIBUTE_CLIENT_ID);
        if (!$clientId) {
            return null;
        }

        $user = $this->userRepo->getByClientId($clientId);

        return $user;
    }
}
