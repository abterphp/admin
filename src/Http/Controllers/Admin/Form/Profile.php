<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Constant\Route;
use AbterPhp\Admin\Form\Factory\Profile as FormFactory;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Profile extends User
{
    /**
     * Profile constructor.
     *
     * @param FlashService     $flashService
     * @param LoggerInterface  $logger
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param Repo             $repo
     * @param ISession         $session
     * @param FormFactory      $formFactory
     * @param IEventDispatcher $eventDispatcher
     * @param AssetManager     $assetManager
     * @param string           $frontendSalt
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Repo $repo,
        ISession $session,
        FormFactory $formFactory,
        IEventDispatcher $eventDispatcher,
        AssetManager $assetManager,
        string $frontendSalt
    ) {
        parent::__construct(
            $flashService,
            $logger,
            $translator,
            $urlGenerator,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher,
            $assetManager,
            $frontendSalt
        );
    }

    public function profile()
    {
        $userId = (string)$this->session->get(Session::USER_ID);

        $this->edit($userId);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $id
     *
     * @return string
     * @throws \Opulence\Routing\Urls\URLException
     */
    protected function getEditUrl(string $id): string
    {
        $urlGenerator = $this->urlGenerator;

        $url = $urlGenerator->createFromName(Route::PROFILE_EDIT);

        return $url;
    }
}
