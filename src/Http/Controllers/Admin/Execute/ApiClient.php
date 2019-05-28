<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Service\Execute\ApiClient as RepoService;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Http\Controllers\Admin\ExecuteAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

class ApiClient extends ExecuteAbstract
{
    const ENTITY_SINGULAR = 'apiClient';
    const ENTITY_PLURAL   = 'apiClients';

    const ENTITY_TITLE_SINGULAR = 'admin:apiClient';
    const ENTITY_TITLE_PLURAL   = 'admin:apiClients';

    /**
     * ApiClient constructor.
     *
     * @param FlashService    $flashService
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param LoggerInterface $logger
     * @param RepoService     $repoService
     * @param ISession        $session
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        LoggerInterface $logger,
        RepoService $repoService,
        ISession $session
    ) {
        parent::__construct(
            $flashService,
            $translator,
            $urlGenerator,
            $logger,
            $repoService,
            $session
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