<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Views\Builders;

use AbterPhp\Admin\Constant\Event;
use AbterPhp\Admin\Events\AdminReady;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Constant\View;
use League\Flysystem\FilesystemException;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

/**
 * Defines the view builder for the login page
 */
class LoginBuilder implements IViewBuilder
{
    protected AssetManager $assets;

    protected IEventDispatcher $eventDispatcher;

    /**
     * AdminBuilder constructor.
     *
     * @param AssetManager     $assets
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(AssetManager $assets, IEventDispatcher $eventDispatcher)
    {
        $this->assets          = $assets;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritdoc
     * @throws FilesystemException
     */
    public function build(IView $view): IView
    {
        $this->assets->addJs(View::ASSET_HEADER, '/admin-assets/vendor/jquery/jquery.min.js');

        $view->setVar('env', getenv(Env::ENV_NAME));
        $view->setVar('title', 'Login');
        $view->setVar('page', '');
        $view->setVar('pageHeader', '');
        $view->setVar('pageFooter', '');

        $view->setVar('preHeader', '');
        $view->setVar('header', '');
        $view->setVar('postHeader', '');

        $view->setVar('preFooter', '');
        $view->setVar('footer', '');
        $view->setVar('postFooter', '');

        $this->eventDispatcher->dispatch(Event::LOGIN_READY, new AdminReady($view));

        $this->assets->addJs(View::ASSET_FOOTER, '/admin-assets/js/alerts.js');
        $this->assets->addJs(View::ASSET_LOGIN, '/admin-assets/vendor/sha3/sha3.min.js');
        $this->assets->addJs(View::ASSET_LOGIN, '/admin-assets/js/login.js');

        return $view;
    }
}
