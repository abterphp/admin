<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Psr7\RequestConverter;
use AbterPhp\Admin\Psr7\ResponseConverter;
use AbterPhp\Admin\Psr7\ResponseFactory;
use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use AbterPhp\Framework\Session\FlashService;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Opulence\Http\Responses\Response;
use Psr\Log\LoggerInterface;

class AccessToken extends ControllerAbstract
{
    /** @var AuthorizationServer */
    protected $authorizationServer;

    /** @var RequestConverter */
    protected $requestConverter;

    /** @var ResponseFactory */
    protected $responseFactory;

    /** @var ResponseConverter */
    protected $responseConverter;

    /**
     * AccessToken constructor.
     *
     * @param FlashService        $flashService
     * @param LoggerInterface     $logger
     * @param AuthorizationServer $authorizationServer
     * @param RequestConverter    $requestConverter
     * @param ResponseFactory     $responseFactory
     * @param ResponseConverter   $responseConverter
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        AuthorizationServer $authorizationServer,
        RequestConverter $requestConverter,
        ResponseFactory $responseFactory,
        ResponseConverter $responseConverter
    ) {
        parent::__construct($flashService, $logger);

        $this->authorizationServer = $authorizationServer;

        $this->requestConverter  = $requestConverter;
        $this->responseFactory   = $responseFactory;
        $this->responseConverter = $responseConverter;
    }

    /**
     * @return Response
     */
    public function create(): Response
    {
        $psr7Request  = $this->requestConverter->toPsr($this->request);
        $prs7Response = $this->responseFactory->create();

        try {
            $prs7Response = $this->authorizationServer->respondToAccessTokenRequest($psr7Request, $prs7Response);
        } catch (OAuthServerException $e) {
            // TODO: error in response...
            $this->logger->info($e->getMessage());
        }

        $response = $this->responseConverter->fromPsr($prs7Response);

        return $response;
    }
}
