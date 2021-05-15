<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Factory\Table\Header;

use AbterPhp\Admin\Grid\Factory\Table\HeaderFactory;

class ApiClient extends HeaderFactory
{
    public const GROUP_ID          = 'apiclient-id';
    public const GROUP_DESCRIPTION = 'apiclient-description';

    private const HEADER_ID          = 'admin:apiClientId';
    private const HEADER_DESCRIPTION = 'admin:apiClientDescription';

    /** @var array<string,string> */
    protected array $headers = [
        self::GROUP_ID          => self::HEADER_ID,
        self::GROUP_DESCRIPTION => self::HEADER_DESCRIPTION,
    ];

    /** @var array<string,string> */
    protected array $inputNames = [
        self::GROUP_DESCRIPTION => 'description',
    ];

    /** @var array<string,string> */
    protected array $fieldNames = [
        self::GROUP_DESCRIPTION => 'ac.description',
    ];
}
