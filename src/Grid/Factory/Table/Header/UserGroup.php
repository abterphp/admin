<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;

class UserGroup extends HeaderFactory
{
    const GROUP_IDENTIFIER = 'userGroup-identifier';
    const GROUP_NAME       = 'userGroup-name';

    const HEADER_IDENTIFIER = 'admin:userGroupIdentifier';
    const HEADER_NAME       = 'admin:userGroupName';

    /** @var array */
    protected $headers = [
        self::GROUP_IDENTIFIER => self::HEADER_IDENTIFIER,
        self::GROUP_NAME       => self::HEADER_NAME,
    ];

    /** @var array */
    protected $inputNames = [
        self::GROUP_IDENTIFIER => 'identifier',
        self::GROUP_NAME       => 'name',
    ];

    /** @var array */
    protected $fieldNames = [
        self::GROUP_IDENTIFIER => 'ug.identifier',
        self::GROUP_NAME       => 'ug.name',
    ];
}
