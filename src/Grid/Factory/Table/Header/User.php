<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;

class User extends HeaderFactory
{
    public const GROUP_USERNAME = 'user-username';
    public const GROUP_EMAIL    = 'user-email';

    private const HEADER_USERNAME = 'admin:userUsername';
    private const HEADER_EMAIL    = 'admin:userEmail';

    /** @var array<string,string> */
    protected array $headers = [
        self::GROUP_USERNAME => self::HEADER_USERNAME,
        self::GROUP_EMAIL    => self::HEADER_EMAIL,
    ];

    /** @var array<string,string> */
    protected array $inputNames = [
        self::GROUP_USERNAME => 'username',
        self::GROUP_EMAIL    => 'email',
    ];

    /** @var array<string,string> */
    protected array $fieldNames = [
        self::GROUP_USERNAME => 'users.username',
        self::GROUP_EMAIL    => 'users.email',
    ];
}
