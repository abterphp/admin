<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Http\Controllers\ApiAbstract;
use AbterPhp\Admin\Service\Execute\UserLanguage as RepoService;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use Psr\Log\LoggerInterface;

class UserLanguage extends ApiAbstract
{
    public const ENTITY_SINGULAR = 'userLanguage';
    public const ENTITY_PLURAL   = 'userLanguages';

    /**
     * UserLanguage constructor.
     *
     * @param LoggerInterface $logger
     * @param RepoService     $repoService
     * @param FoundRows       $foundRows
     * @param string          $problemBaseUrl
     */
    public function __construct(
        LoggerInterface $logger,
        RepoService $repoService,
        FoundRows $foundRows,
        string $problemBaseUrl
    ) {
        parent::__construct($logger, $repoService, $foundRows, $problemBaseUrl);
    }
}
