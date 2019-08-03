<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;

class ApiClient extends HeaderFactory
{
    const GROUP_ID          = 'apiclient-id';
    const GROUP_DESCRIPTION = 'apiclient-description';

    const HEADER_ID          = 'admin:apiClientId';
    const HEADER_DESCRIPTION = 'admin:apiClientDescription';

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
        self::GROUP_DESCRIPTION => 'ac.description',
    ];
}
