<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Http\Controllers\Admin\AdminAbstract;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Urls\URLException;
use Opulence\Routing\Urls\UrlGenerator;
use Psr\Log\LoggerInterface;

class Login extends AdminAbstract
{
    const ENTITY_SINGULAR = 'login';

    const POST_USERNAME = 'username';

    /** @var AssetManager */
    protected $assets;

    /** @var string */
    protected $frontendSalt;

    /**
     * Login constructor.
     *
     * @param FlashService    $flashService
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param LoggerInterface $logger
     * @param AssetManager    $assets
     * @param string          $frontendSalt
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger,
        AssetManager $assets,
        string $frontendSalt
    ) {
        parent::__construct($flashService, $translator, $urlGenerator, $logger);

        $this->assets       = $assets;
        $this->frontendSalt = $frontendSalt;
    }

    /**
     * @return Response
     * @throws URLException
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
