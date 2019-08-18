<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\RepoGrid;

use AbterPhp\Admin\Grid\Factory\User as GridFactory;
use AbterPhp\Admin\Orm\UserRepo as Repo;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use Casbin\Enforcer;

class User extends RepoGridAbstract
{
    /**
     * User constructor.
     *
     * @param Enforcer    $enforcer
     * @param Repo        $repo
     * @param FoundRows   $foundRows
     * @param GridFactory $gridFactory
     */
    public function __construct(Enforcer $enforcer, Repo $repo, FoundRows $foundRows, GridFactory $gridFactory)
    {
        parent::__construct($enforcer, $repo, $foundRows, $gridFactory);
    }
}
