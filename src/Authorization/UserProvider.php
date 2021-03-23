<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\IAuthLoader;
use AbterPhp\Admin\Databases\Queries\UserAuthLoader;
use Casbin\Exceptions\CasbinException;
use Casbin\Model\Model;
use Casbin\Persist\Adapter as CasbinAdapter;

class UserProvider implements CasbinAdapter
{
    /** @var IAuthLoader */
    protected IAuthLoader $userAuth;

    /**
     * UserProvider constructor.
     *
     * @param UserAuthLoader $userAuth
     */
    public function __construct(UserAuthLoader $userAuth)
    {
        $this->userAuth = $userAuth;
    }

    /**
     * @param Model $model
     */
    public function loadPolicy(Model $model): void
    {
        $rawData = $this->userAuth->loadAll();

        foreach ($rawData as $line) {
            $model->addPolicy('g', 'g', [$line['v0'], $line['v1'], '', '', ',']);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Model $model
     */
    public function savePolicy(Model $model): void
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $sec
     * @param string $ptype
     * @param int    $fieldIndex
     * @param string ...$fieldValues
     *
     * @throws CasbinException
     */
    public function removeFilteredPolicy(string $sec, string $ptype, int $fieldIndex, string ...$fieldValues): void
    {
        throw new CasbinException('not implemented');
    }
}
