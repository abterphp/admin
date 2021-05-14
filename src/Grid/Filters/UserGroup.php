<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Filters;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Filter\ExactFilter;
use AbterPhp\Framework\Grid\Filter\LikeFilter;

class UserGroup extends Filters
{
    /**
     * UserGroup constructor.
     *
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function __construct(array $intents = [], array $attributes = [], ?string $tag = null)
    {
        parent::__construct($intents, $attributes, $tag);

        $this->add(new ExactFilter('identifier', 'admin:userGroupIdentifier'));
        $this->add(new LikeFilter('name', 'admin:userGroupName'));
    }
}
