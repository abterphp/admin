<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Service\Execute\ApiClient as RepoService;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Http\Controllers\Admin\ApiAbstract;
use Psr\Log\LoggerInterface;

class ApiClient extends ApiAbstract
{
    const ENTITY_SINGULAR = 'apiClient';
    const ENTITY_PLURAL   = 'apiClients';

    /**
     * ApiClient constructor.
     *
     * @param LoggerInterface $logger
     * @param RepoService     $repoService
     * @param FoundRows       $foundRows
     */
    public function __construct(LoggerInterface $logger, RepoService $repoService, FoundRows $foundRows)
    {
        parent::__construct($logger, $repoService, $foundRows);
    }
}
