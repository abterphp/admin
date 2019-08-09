<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service\RepoGrid;

use AbterPhp\Admin\Grid\Factory\ApiClient as GridFactory;
use AbterPhp\Admin\Http\Service\RepoGrid\RepoGridAbstract;
use AbterPhp\Admin\Orm\ApiClientRepo as Repo;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use AbterPhp\Framework\Grid\IGrid;
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
     * @param IGrid $grid
     *
     * @return array
     */
    protected function getWhereConditions(IGrid $grid): array
    {
        $conditions = $grid->getWhereConditions();

        $conditions[] = 'ac.user_id = ?';

        return $conditions;
    }

    /**
     * @param IGrid $grid
     *
     * @return array
     */
    protected function getSqlParams(IGrid $grid): array
    {
        $sqlParams = $grid->getSqlParams();

        $userId = $this->session->get(Session::USER_ID);

        $sqlParams[] = $userId;

        return $sqlParams;
    }
}
