<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\AdminResourceAuthLoader as AuthLoader;
use Casbin\Exceptions\CasbinException;
use Casbin\Model\Model;
use Casbin\Persist\Adapter as CasbinAdapter;

class AdminResourceProvider implements CasbinAdapter
{
    use PolicyProviderTrait;

    protected const PREFIX = 'admin_resource';

    /**
     * AdminResourceProvider constructor.
     *
     * @param AuthLoader $authLoader
     */
    public function __construct(AuthLoader $authLoader)
    {
        $this->authLoader = $authLoader;
        $this->prefix     = static::PREFIX;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Model $model
     *
     * @throws CasbinException
     */
    public function savePolicy(Model $model): void
    {
        throw new CasbinException('not implemented');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     *
     * @throws CasbinException
     */
    public function addPolicy(string $sec, string $ptype, array $rule): void
    {
        throw new CasbinException('not implemented');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param string $sec
     * @param string $ptype
     * @param array  $rule
     *
     * @throws CasbinException
     */
    public function removePolicy(string $sec, string $ptype, array $rule): void
    {
        throw new CasbinException('not implemented');
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
