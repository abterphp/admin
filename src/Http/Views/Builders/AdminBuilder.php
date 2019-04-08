<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Views\Builders;

use AbterPhp\Admin\Constant\Event;
use AbterPhp\Admin\Events\AdminReady;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Constant\View;
use AbterPhp\Framework\Navigation\Navigation;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Sessions\ISession;
use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

/**
 * Defines a view builder for the admin pages
 */
class AdminBuilder implements IViewBuilder
{
    /** @var ISession */
    protected $session;

    /** @var AssetManager */
    protected $assets;

    /** @var IEventDispatcher */
    protected $eventDispatcher;

    /** @var Navigation|null */
    protected $primaryNav;

    /** @var Navigation|null */
    protected $navbar;

    /**
     * AdminBuilder constructor.
     *
     * @param ISession         $session
     * @param AssetManager     $assets
     * @param IEventDispatcher $eventDispatcher
     * @param Navigation|null  $primaryNav
     * @param Navigation|null  $navbar
     */
    public function __construct(
        ISession $session,
        AssetManager $assets,
        IEventDispatcher $eventDispatcher,
        ?Navigation $primaryNav,
        ?Navigation $navbar
    ) {
        $this->session         = $session;
        $this->assets          = $assets;
        $this->eventDispatcher = $eventDispatcher;
        $this->primaryNav      = $primaryNav;
        $this->navbar          = $navbar;
    }

    /**
     * @inheritdoc
     */
    public function build(IView $view): IView
    {
        $this->assets->addJs(View::ASSET_HEADER, '/admin-assets/vendor/jquery/jquery.min.js');

        $view->setVar('env', getenv(Env::ENV_NAME));
        $view->setVar('title', 'Admin');
        $view->setVar('username', $this->session->get(Session::USERNAME));
        $view->setVar('primaryNav', $this->primaryNav);
        $view->setVar('navbar', $this->navbar);

        $view->setVar('preHeader', '');
        $view->setVar('header', '');
        $view->setVar('postHeader', '');

        $view->setVar('preFooter', '');
        $view->setVar('footer', '');
        $view->setVar('postFooter', '');

        $this->eventDispatcher->dispatch(Event::ADMIN_READY, new AdminReady($view));

        $this->assets->addJs(View::ASSET_FOOTER, '/admin-assets/js/alerts.js');

        return $view;
    }
}
