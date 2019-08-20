<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Middleware;

use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Domain\Entities\UserLanguage;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Admin\Psr7\RequestConverter;
use AbterPhp\Admin\Psr7\ResponseConverter;
use AbterPhp\Admin\Psr7\ResponseFactory;
use AbterPhp\Framework\Config\EnvReader;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestMethods;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Nyholm\Psr7\ServerRequest as Psr7Request;

class ApiTest extends TestCase
{
    /** @var Api - System Under Test */
    protected $sut;

    /** @var MockObject|ResourceServer */
    protected $resourceServerMock;

    /** @var MockObject|RequestConverter */
    protected $requestConverterMock;

    /** @var MockObject|UserRepo */
    protected $userRepoMock;

    /** @var MockObject|LoggerInterface */
    protected $loggerMock;

    /** @var MockObject|EnvReader */
    protected $envReaderMock;

    public function setUp(): void
    {
        $this->resourceServerMock   = $this->createMock(ResourceServer::class);
        $this->requestConverterMock = $this->createMock(RequestConverter::class);
        $this->userRepoMock         = $this->createMock(UserRepo::class);
        $this->loggerMock           = $this->createMock(LoggerInterface::class);
        $this->envReaderMock        = $this->createMock(EnvReader::class);

        $this->sut = new Api(
            $this->resourceServerMock,
            $this->requestConverterMock,
            $this->userRepoMock,
            $this->loggerMock,
            $this->envReaderMock
        );
    }

    public function testHandleReturnsJsonResponseOnValidationFailure()
    {
        $errorMsg        = 'Foo failure';
        $errorCode       = 17;
        $errorType       = 'FOO';
        $errorStatusCode = ResponseHeaders::HTTP_GONE;

        $exception = new OAuthServerException($errorMsg, $errorCode, $errorType, $errorStatusCode);

        $this->resourceServerMock
            ->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->willThrowException($exception);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertInstanceOf(Response::class, $actualResult);
        $this->assertSame($errorStatusCode, $actualResult->getStatusCode());
        $this->assertJson($actualResult->getContent());
    }

    public function testHandleReturnsJsonResponseOnValidationError()
    {
        $exception = new \Exception();

        $this->resourceServerMock
            ->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->willThrowException($exception);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertInstanceOf(Response::class, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $actualResult->getStatusCode());
        $this->assertJson($actualResult->getContent());
    }

    public function testHandleRetrievesUserByUserId()
    {
        $uri      = 'https://example.com/foo';
        $username = 'Foo Lee';
        $userId   = '604155e4-10f2-4d2d-857b-97d841317e5c';

        $psr7Request = new Psr7Request(RequestMethods::GET, $uri);
        $psr7Request = $psr7Request->withAttribute(Api::ATTRIBUTE_USER_ID, $userId);

        $this->resourceServerMock
            ->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->willReturn($psr7Request);

        $languageStub = new UserLanguage('', '', '');
        $userStub     = new User($userId, $username, '', '', true, true, $languageStub);

        $this->userRepoMock->expects($this->once())->method('getById')->willReturn($userStub);
        $this->userRepoMock->expects($this->never())->method('getByClientId');

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_OK, $actualResult->getStatusCode());

        $headers = $requestStub->getHeaders();
        $this->assertArrayHasKey(Api::HEADER_USER_ID, $headers);
        $this->assertArrayHasKey(Api::HEADER_USER_USERNAME, $headers);
        $this->assertSame($userId, $headers[Api::HEADER_USER_ID]);
        $this->assertSame($username, $headers[Api::HEADER_USER_USERNAME]);
    }

    public function testHandleRetrievesUserByClientIdIfUserIdIsNotSet()
    {
        $uri      = 'https://example.com/foo';
        $username = 'Foo Lee';
        $userId   = '604155e4-10f2-4d2d-857b-97d841317e5c';
        $clientId = '1fde89de-9f5b-4d90-a0a5-072ea3cfc7b3';

        $psr7Request = (new Psr7Request(RequestMethods::GET, $uri))
            ->withAttribute(Api::ATTRIBUTE_CLIENT_ID, $clientId);

        $this->resourceServerMock
            ->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->willReturn($psr7Request);

        $languageStub = new UserLanguage('', '', '');
        $userStub     = new User($userId, $username, '', '', true, true, $languageStub);

        $this->userRepoMock->expects($this->never())->method('getById');
        $this->userRepoMock->expects($this->once())->method('getByClientId')->willReturn($userStub);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_OK, $actualResult->getStatusCode());

        $headers = $requestStub->getHeaders();
        $this->assertArrayHasKey(Api::HEADER_USER_ID, $headers);
        $this->assertArrayHasKey(Api::HEADER_USER_USERNAME, $headers);
        $this->assertSame($userId, $headers[Api::HEADER_USER_ID]);
        $this->assertSame($username, $headers[Api::HEADER_USER_USERNAME]);
    }

    public function testHandleRetrievesUserByClientIdIfUserIsNotFoundById()
    {
        $uri      = 'https://example.com/foo';
        $username = 'Foo Lee';
        $userId   = '604155e4-10f2-4d2d-857b-97d841317e5c';
        $clientId = '1fde89de-9f5b-4d90-a0a5-072ea3cfc7b3';

        $psr7Request = (new Psr7Request(RequestMethods::GET, $uri))
            ->withAttribute(Api::ATTRIBUTE_USER_ID, $userId)
            ->withAttribute(Api::ATTRIBUTE_CLIENT_ID, $clientId);

        $this->resourceServerMock
            ->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->willReturn($psr7Request);

        $languageStub = new UserLanguage('', '', '');
        $userStub     = new User($userId, $username, '', '', true, true, $languageStub);

        $this->userRepoMock->expects($this->once())->method('getById')->willReturn(null);
        $this->userRepoMock->expects($this->once())->method('getByClientId')->willReturn($userStub);

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertSame($responseStub, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_OK, $actualResult->getStatusCode());

        $headers = $requestStub->getHeaders();
        $this->assertArrayHasKey(Api::HEADER_USER_ID, $headers);
        $this->assertArrayHasKey(Api::HEADER_USER_USERNAME, $headers);
        $this->assertSame($userId, $headers[Api::HEADER_USER_ID]);
        $this->assertSame($username, $headers[Api::HEADER_USER_USERNAME]);
    }

    public function testHandleReturnsJsonResponseOnUserRetrievalError()
    {
        $uri = 'https://example.com/foo';

        $psr7Request = new Psr7Request(RequestMethods::GET, $uri);

        $this->resourceServerMock
            ->expects($this->once())
            ->method('validateAuthenticatedRequest')
            ->willReturn($psr7Request);

        $this->userRepoMock->expects($this->never())->method('getById');
        $this->userRepoMock->expects($this->never())->method('getByClientId');

        $requestStub  = new Request([], [], [], [], [], [], null);
        $responseStub = new Response();

        $next = function () use ($responseStub) {
            return $responseStub;
        };

        $actualResult = $this->sut->handle($requestStub, $next);

        $this->assertInstanceOf(Response::class, $actualResult);
        $this->assertSame(ResponseHeaders::HTTP_INTERNAL_SERVER_ERROR, $actualResult->getStatusCode());
        $this->assertJson($actualResult->getContent());
    }
}
