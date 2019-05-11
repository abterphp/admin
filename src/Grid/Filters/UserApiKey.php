<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Filters;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Filter\LikeFilter;

class UserApiKey extends Filters
{
    /**
     * User constructor.
     *
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(array $intents = [], array $attributes = [], ?string $tag = null)
    {
        parent::__construct($intents, $attributes, $tag);

        $this->nodes[] = new LikeFilter('description', 'admin:userApiKeyDescription');
    }
}
