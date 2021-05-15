<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Service\Login as LoginService;
use AbterPhp\Admin\Service\SessionInitializer;
use AbterPhp\Framework\Http\Controllers\ControllerAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Orm\OrmException;
use Opulence\QueryBuilders\InvalidQueryException;
use Psr\Log\LoggerInterface;

class Login extends ControllerAbstract
{
    public const REMOTE_ADDR = 'REMOTE_ADDR';

    public const POST_USERNAME = 'username';
    public const POST_PASSWORD = 'password';

    public const ERROR_MSG_LOGIN_THROTTLED    = 'admin:throttled';
    public const ERROR_MSG_DB_PROBLEM         = 'admin:dbProblem';
    public const ERROR_MSG_UNEXPECTED_PROBLEM = 'admin:unexpectedProblem';
    public const ERROR_MSG_LOGIN_FAILED       = 'admin:unknownFailure';

    public const SUCCESS_MSG = 'User "%s" logged in.';

    protected SessionInitializer $sessionInitializer;

    protected ITranslator $translator;

    protected LoginService $loginService;

    protected RoutesConfig $routesConfig;

    /**
     * Login constructor.
     *
     * @param FlashService       $flashService
     * @param LoggerInterface    $logger
     * @param SessionInitializer $sessionInitializer
     * @param ITranslator        $translator
     * @param LoginService       $loginService
     * @param RoutesConfig       $routesConfig
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        SessionInitializer $sessionInitializer,
        ITranslator $translator,
        LoginService $loginService,
        RoutesConfig $routesConfig
    ) {
        parent::__construct($flashService, $logger);

        $this->sessionInitializer = $sessionInitializer;
        $this->translator         = $translator;
        $this->loginService       = $loginService;
        $this->routesConfig       = $routesConfig;
    }

    public function execute(): Response
    {
        $ipAddress = (string)$this->request->getServer()->get(static::REMOTE_ADDR);
        $username  = (string)$this->request->getInput(static::POST_USERNAME);
        $password  = (string)$this->request->getInput(static::POST_PASSWORD);

        try {
            if (!$this->loginService->isLoginAllowed($username, $ipAddress)) {
                $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_LOGIN_THROTTLED)]);

                return new RedirectResponse($this->routesConfig->getLoginFailurePath());
            }

            $user = $this->loginService->login($username, $password, $ipAddress);
            if ($user) {
                $this->logger->info(sprintf(static::SUCCESS_MSG, $user->getUsername()));

                $this->sessionInitializer->initialize($user);

                return new RedirectResponse($this->routesConfig->getLoginSuccessPath());
            } else {
                $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_LOGIN_FAILED)]);
            }
        } catch (OrmException | InvalidQueryException $e) {
            $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_DB_PROBLEM)]);
        } catch (\Exception $e) {
            $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_UNEXPECTED_PROBLEM)]);
        }

        return new RedirectResponse($this->routesConfig->getLoginFailurePath());
    }

    /**
     * @param string $messageType
     *
     * @return string
     */
    protected function translate(string $messageType): string
    {
        return $this->translator->translate($messageType);
    }
}
