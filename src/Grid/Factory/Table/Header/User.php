<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;

class User extends HeaderFactory
{
    const GROUP_USERNAME = 'user-username';
    const GROUP_EMAIL    = 'user-email';

    const HEADER_USERNAME = 'admin:userUsername';
    const HEADER_EMAIL    = 'admin:userEmail';

    /** @var array */
    protected $headers = [
        self::GROUP_USERNAME => self::HEADER_USERNAME,
        self::GROUP_EMAIL    => self::HEADER_EMAIL,
    ];

    /** @var array */
    protected $inputNames = [
        self::GROUP_USERNAME => 'username',
        self::GROUP_EMAIL    => 'email',
    ];

    /** @var array */
    protected $fieldNames = [
        self::GROUP_USERNAME => 'users.username',
        self::GROUP_EMAIL    => 'users.email',
    ];
}
