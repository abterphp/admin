<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Constant\Routes;
use AbterPhp\Admin\Form\Factory\Profile as FormFactory;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;

class Profile extends User
{
    /**
     * User constructor.
     *
     * @param FlashService     $flashService
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param Repo             $repo
     * @param ISession         $session
     * @param FormFactory      $formFactory
     * @param AssetManager     $assetManager
     * @param IEventDispatcher $eventDispatcher
     * @param string           $frontendSalt
     */
    public function __construct(
        FlashService $flashService,
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
        $userId = $this->session->get(Session::USER_ID);

        $this->edit($userId);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $id
     *
     * @return string
     * @throws URLException
     */
    protected function getEditUrl(string $id): string
    {
        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->urlGenerator;

        $url = $urlGenerator->createFromName(Routes::ROUTE_PROFILE);

        return $url;
    }
}
