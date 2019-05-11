<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Framework\Grid\Factory\Table\HeaderFactory;

class UserApiKey extends HeaderFactory
{
    const GROUP_ID          = 'userapikey-id';
    const GROUP_DESCRIPTION = 'userapikey-description';

    const HEADER_ID          = 'admin:userApiKeyId';
    const HEADER_DESCRIPTION = 'admin:userApiKeyDescription';

    /** @var array */
    protected $headers = [
        self::GROUP_ID          => self::HEADER_ID,
        self::GROUP_DESCRIPTION => self::HEADER_DESCRIPTION,
    ];

    /** @var array */
    protected $inputNames = [
        self::GROUP_DESCRIPTION => 'description',
    ];

    /** @var array */
    protected $fieldNames = [
        self::GROUP_DESCRIPTION => 'uak.description',
    ];
}
