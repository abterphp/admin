<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Service\Execute\ApiClient as RepoService;
use AbterPhp\Framework\Config\EnvReader;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Http\Controllers\ApiAbstract;
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
     * @param EnvReader       $envReader
     */
    public function __construct(
        LoggerInterface $logger,
        RepoService $repoService,
        FoundRows $foundRows,
        EnvReader $envReader
    ) {
        parent::__construct($logger, $repoService, $foundRows, $envReader);
    }
}
