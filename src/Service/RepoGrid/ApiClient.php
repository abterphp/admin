<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\RepoGrid;

use AbterPhp\Admin\Grid\Factory\ApiClient as GridFactory;
use AbterPhp\Admin\Orm\ApiClientRepo as Repo;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Grid\Grid;
use AbterPhp\Framework\Http\Service\RepoGrid\RepoGridAbstract;
use Casbin\Enforcer;
use Opulence\Sessions\ISession;

class ApiClient extends RepoGridAbstract
{
    /** @var ISession */
    protected $session;

    /**
     * ApiClient constructor.
     *
     * @param Enforcer    $enforcer
     * @param Repo        $repo
     * @param FoundRows   $foundRows
     * @param GridFactory $gridFactory
     * @param ISession    $session
     */
    public function __construct(
        Enforcer $enforcer,
        Repo $repo,
        FoundRows $foundRows,
        GridFactory $gridFactory,
        ISession $session
    ) {
        parent::__construct($enforcer, $repo, $foundRows, $gridFactory);

        $this->session = $session;
    }

    /**
     * @param Grid $grid
     *
     * @return array
     */
    protected function getWhereConditions(Grid $grid): array
    {
        $conditions = $grid->getWhereConditions();

        $conditions[] = 'ac.user_id = ?';

        return $conditions;
    }

    /**
     * @param Grid $grid
     *
     * @return array
     */
    protected function getSqlParams(Grid $grid): array
    {
        $sqlParams = $grid->getSqlParams();

        $userId = $this->session->get(Session::USER_ID);

        $sqlParams[] = $userId;

        return $sqlParams;
    }
}
