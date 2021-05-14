<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table;

use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Body;

class BodyFactory
{
    /**
     * @param array<string,callable> $getters
     * @param Actions|null           $actions
     *
     * @return Body
     */
    public function create(
        array $getters,
        ?Actions $actions = null
    ): Body {
        return new Body($getters, $actions);
    }
}
