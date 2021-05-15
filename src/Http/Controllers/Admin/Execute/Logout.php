<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Http\Controllers\Admin\AdminAbstract;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\RedirectResponse;
use Opulence\Http\Responses\Response;
use Opulence\Http\Responses\ResponseHeaders;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

class Logout extends AdminAbstract
{
    public const SUCCESS_MSG = 'User "%s" logged out.';

    protected ISession $session;

    protected RoutesConfig $routesConfig;

    /**
     * Logout constructor.
     *
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param ISession        $session
     * @param RoutesConfig    $routesConfig
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        ISession $session,
        RoutesConfig $routesConfig
    ) {
        parent::__construct($flashService, $logger, $translator, $urlGenerator);

        $this->session      = $session;
        $this->routesConfig = $routesConfig;
    }

    /**
     * @return Response
     */
    public function execute(): Response
    {
        $username = $this->session->get(Session::USERNAME);

        $this->session->flush();

        $this->logger->info(sprintf(static::SUCCESS_MSG, $username));

        return new RedirectResponse($this->routesConfig->getLoginPath(), ResponseHeaders::HTTP_FOUND);
    }
}
