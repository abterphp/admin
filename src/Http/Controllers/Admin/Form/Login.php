<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Http\Controllers\Admin\AdminAbstract;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

class Login extends AdminAbstract
{
    const ENTITY_SINGULAR = 'login';

    /** @var AssetManager */
    protected $assets;

    /** @var string */
    protected $frontendSalt;

    /**
     * Login constructor.
     *
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param AssetManager    $assets
     * @param string          $frontendSalt
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        AssetManager $assets,
        string $frontendSalt
    ) {
        parent::__construct($flashService, $logger, $translator, $urlGenerator);

        $this->assets       = $assets;
        $this->frontendSalt = $frontendSalt;
    }

    /**
     * @return Response
     * @throws \Throwable
     */
    public function display(): Response
    {
        $this->assets->addJsVar('admin-login', 'frontendSalt', $this->frontendSalt);

        $this->view = $this->viewFactory->createView('contents/backend/login');

        $title = $this->translator->translate("admin:login");

        return $this->createResponse($title);
    }
}
