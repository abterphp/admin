<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Admin\Databases\Queries\IAuthLoader;
use Casbin\Model\Model;

trait PolicyProviderTrait
{
    protected IAuthLoader $authLoader;

    protected string $prefix = '';

    /**
     * @param Model $model
     */
    public function loadPolicy(Model $model): void
    {
        $rawData = $this->authLoader->loadAll();

        foreach ($rawData as $line) {
            $v0 = $line['v0'];
            $v1 = sprintf('%s_%s', $this->prefix, $line['v1']);

            $model->addPolicy('p', 'p', [$v0, $v1, 'read', '', ',']);
            $model->addPolicy('p', 'p', [$v0, $v1, 'write', '', ',']);
        }
    }
}
