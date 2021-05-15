<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\Dashboard\Dashboard as DashboardCollection;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Casbin\Enforcer;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;
use Throwable;

class Dashboard extends AdminAbstract
{
    public const ENTITY_SINGULAR = 'dashboard';

    public const TITLE_DASHBOARD = 'admin:dashboard';

    protected ITranslator $translator;

    protected Enforcer $enforcer;

    protected DashboardCollection $dashboard;

    /**
     * Dashboard constructor.
     *
     * @param FlashService        $flashService
     * @param LoggerInterface     $logger
     * @param ITranslator         $translator
     * @param UrlGenerator        $urlGenerator
     * @param Enforcer            $enforcer
     * @param DashboardCollection $dashboard
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Enforcer $enforcer,
        DashboardCollection $dashboard
    ) {
        parent::__construct($flashService, $logger, $translator, $urlGenerator);

        $this->enforcer  = $enforcer;
        $this->dashboard = $dashboard;
    }

    /**
     * @return Response
     * @throws Throwable
     */
    public function showDashboard(): Response
    {
        $title = $this->translator->translate(static::TITLE_DASHBOARD);

        $this->view = $this->viewFactory->createView('contents/backend/dashboard');

        $this->addCustomAssets();

        $this->view->setVar('dashboard', $this->dashboard);

        return $this->createResponse($title);
    }
}
