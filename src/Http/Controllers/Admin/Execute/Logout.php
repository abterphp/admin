<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Http\Controllers\Admin\AdminAbstract;
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
    const SUCCESS_MSG = 'User "%s" logged out.';

    /** @var ISession */
    protected $session;

    /**
     * Logout constructor.
     *
     * @param FlashService    $flashService
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param LoggerInterface $logger
     * @param ISession        $session
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger,
        ISession $session
    ) {
        parent::__construct($flashService, $translator, $urlGenerator, $logger);

        $this->session = $session;
    }

    /**
     * @return Response
     */
    public function execute(): Response
    {
        $username = $this->session->get(Session::USERNAME);

        $this->session->flush();

        $this->logger->info(sprintf(static::SUCCESS_MSG, $username));

        return new RedirectResponse(PATH_LOGIN, ResponseHeaders::HTTP_FOUND);
    }
}
