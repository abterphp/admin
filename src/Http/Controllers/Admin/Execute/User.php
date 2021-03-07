<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Admin\Execute;

use AbterPhp\Admin\Http\Controllers\Admin\ExecuteAbstract;
use AbterPhp\Admin\Service\Execute\User as RepoService;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

class User extends ExecuteAbstract
{
    const ENTITY_SINGULAR = 'user';
    const ENTITY_PLURAL   = 'users';

    const ENTITY_TITLE_SINGULAR = 'admin:user';
    const ENTITY_TITLE_PLURAL   = 'admin:users';

    const ROUTING_PATH = 'users';

    /**
     * User constructor.
     *
     * @param FlashService    $flashService
     * @param LoggerInterface $logger
     * @param ITranslator     $translator
     * @param UrlGenerator    $urlGenerator
     * @param RepoService     $repoService
     * @param ISession        $session
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        RepoService $repoService,
        ISession $session
    ) {
        parent::__construct(
            $flashService,
            $logger,
            $translator,
            $urlGenerator,
            $repoService,
            $session
        );
    }
}
