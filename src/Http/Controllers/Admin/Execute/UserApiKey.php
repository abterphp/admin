<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Service\Execute\UserApiKey as RepoService;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Http\Controllers\Admin\ExecuteAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

class UserApiKey extends ExecuteAbstract
{
    const ENTITY_SINGULAR = 'userApiKey';
    const ENTITY_PLURAL   = 'userApiKeys';

    const ENTITY_TITLE_SINGULAR = 'admin:userApiKey';
    const ENTITY_TITLE_PLURAL   = 'admin:userApiKeys';

    /**
     * ApiKey constructor.
     *
     * @param FlashService    $flashService
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param RepoService     $repoService
     * @param ISession        $session
     * @param LoggerInterface $logger
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        RepoService $repoService,
        ISession $session,
        LoggerInterface $logger
    ) {
        parent::__construct(
            $flashService,
            $translator,
            $urlGenerator,
            $repoService,
            $session,
            $logger
        );
    }

    /**
     * @return array
     */
    protected function getPostData(): array
    {
        $postData = $this->request->getPost()->getAll();

        if ($postData) {
            $postData['user_id'] = $this->session->get(Session::USER_ID);
        }

        return $postData;
    }
}
