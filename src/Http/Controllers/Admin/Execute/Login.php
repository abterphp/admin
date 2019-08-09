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
    const REMOTE_ADDR = 'REMOTE_ADDR';

    const POST_USERNAME = 'username';
    const POST_PASSWORD = 'password';

    const ERROR_MSG_LOGIN_THROTTLED    = 'admin:throttled';
    const ERROR_MSG_DB_PROBLEM         = 'admin:dbProblem';
    const ERROR_MSG_UNEXPECTED_PROBLEM = 'admin:unexpectedProblem';
    const ERROR_MSG_LOGIN_FAILED       = 'admin:unknownFailure';

    const SUCCESS_MSG = 'User "%s" logged in.';

    /** @var SessionInitializer */
    protected $sessionInitializer;

    /** @var ITranslator */
    protected $translator;

    /** @var LoginService */
    protected $loginService;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * Login constructor.
     *
     * @param FlashService       $flashService
     * @param SessionInitializer $sessionInitializer
     * @param ITranslator        $translator
     * @param LoginService       $loginService
     * @param LoggerInterface    $logger
     */
    public function __construct(
        FlashService $flashService,
        SessionInitializer $sessionInitializer,
        ITranslator $translator,
        LoginService $loginService,
        LoggerInterface $logger
    ) {
        parent::__construct($flashService);

        $this->sessionInitializer = $sessionInitializer;
        $this->translator         = $translator;
        $this->loginService       = $loginService;
        $this->logger             = $logger;
    }

    public function execute(): Response
    {
        $ipAddress = (string)$this->request->getServer()->get(static::REMOTE_ADDR);
        $username  = (string)$this->request->getInput(static::POST_USERNAME);
        $password  = (string)$this->request->getInput(static::POST_PASSWORD);

        try {
            if (!$this->loginService->isLoginAllowed($username, $ipAddress)) {
                $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_LOGIN_THROTTLED)]);

                return new RedirectResponse(RoutesConfig::getLoginFailurePath());
            }

            $user = $this->loginService->login($username, $password, $ipAddress);
            if ($user) {
                $this->logger->info(sprintf(static::SUCCESS_MSG, $user->getUsername()));

                $this->sessionInitializer->initialize($user);

                return new RedirectResponse(RoutesConfig::getLoginSuccessPath());
            } else {
                $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_LOGIN_FAILED)]);
            }
        } catch (OrmException $e) {
            $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_DB_PROBLEM)]);
        } catch (InvalidQueryException $e) {
            $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_DB_PROBLEM)]);
        } catch (\Exception $e) {
            $this->flashService->mergeErrorMessages([$this->translate(static::ERROR_MSG_UNEXPECTED_PROBLEM)]);
        }

        return new RedirectResponse(RoutesConfig::getLoginFailurePath());
    }

    /**
     * @param string $messageType
     *
     * @return string
     */
    protected function translate(string $messageType)
    {
        return $this->translator->translate($messageType);
    }
}
