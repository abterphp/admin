<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin;

use AbterPhp\Framework\Dashboard\Dashboard as DashboardCollection;
use AbterPhp\Framework\Http\Controllers\Admin\AdminAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Casbin\Enforcer;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Urls\UrlGenerator;

class Dashboard extends AdminAbstract
{
    const ENTITY_SINGULAR = 'dashboard';

    const TITLE_DASHBOARD = 'admin:dashboard';

    /** @var ITranslator */
    protected $translator;

    /** @var Enforcer */
    protected $enforcer;

    /** @var DashboardCollection */
    protected $dashboard;

    /**
     * Dashboard constructor.
     *
     * @param FlashService        $flashService
     * @param ITranslator         $translator
     * @param UrlGenerator        $urlGenerator
     * @param DashboardCollection $dashboard
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Enforcer $enforcer,
        DashboardCollection $dashboard
    ) {
        parent::__construct($flashService, $translator, $urlGenerator);

        $this->enforcer  = $enforcer;
        $this->dashboard = $dashboard;
    }

    /**
     * @return Response
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
