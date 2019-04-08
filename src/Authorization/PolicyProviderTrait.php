<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Authorization;

use AbterPhp\Framework\Databases\Queries\IAuthLoader;
use Casbin\Model\Model;

trait PolicyProviderTrait
{
    /** @var IAuthLoader */
    protected $authQueries;

    /** @var string */
    protected $prefix = '';

    /**
     * @param Model $model
     */
    public function loadPolicy($model)
    {
        $rawData = $this->authQueries->loadAll();

        foreach ($rawData as $line) {
            $v0 = $line['v0'];
            $v1 = sprintf('%s_%s', $this->prefix, $line['v1']);

            $model->model['p']['p']->policy[] = [$v0, $v1, 'read', '', ','];
            $model->model['p']['p']->policy[] = [$v0, $v1, 'write', '', ','];
        }

        return;
    }
}
