<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Service\Execute\UserGroup as RepoService;
use AbterPhp\Framework\Config\EnvReader;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Http\Controllers\ApiAbstract;
use Psr\Log\LoggerInterface;

class UserGroup extends ApiAbstract
{
    const ENTITY_SINGULAR = 'userGroup';
    const ENTITY_PLURAL   = 'userGroups';

    /**
     * UserGroup constructor.
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

    /**
     * @return array
     */
    public function getSharedData(): array
    {
        $data = $this->request->getJsonBody();

        if (array_key_exists('password', $data)) {
            $data['password_repeated'] = $data['password'];
        }

        return $data;
    }
}
