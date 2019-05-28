<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Service\Execute\UserLanguage as RepoService;
use AbterPhp\Framework\Http\Controllers\Admin\ApiAbstract;
use Opulence\Http\Responses\Response;
use Opulence\Routing\Controller;
use Psr\Log\LoggerInterface;

class UserLanguage extends ApiAbstract
{
    const ENTITY_SINGULAR = 'userLanguage';
    const ENTITY_PLURAL   = 'userLanguages';

    /**
     * UserLanguage constructor.
     *
     * @param LoggerInterface $logger
     * @param RepoService     $repoService
     */
    public function __construct(LoggerInterface $logger, RepoService $repoService)
    {
        parent::__construct($logger, $repoService);
    }
}
