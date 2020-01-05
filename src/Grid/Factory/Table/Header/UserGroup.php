<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;

class UserGroup extends HeaderFactory
{
    public const GROUP_NAME = 'userGroup-name';

    private const HEADER_NAME = 'admin:userGroupName';

    /** @var array */
    protected $headers = [
        self::GROUP_NAME => self::HEADER_NAME,
    ];

    /** @var array */
    protected $inputNames = [
        self::GROUP_NAME => 'name',
    ];

    /** @var array */
    protected $fieldNames = [
        self::GROUP_NAME => 'ug.name',
    ];
}
