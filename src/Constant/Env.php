<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Constant;

use AbterPhp\Framework\Constant\Env as FrameworkEnv;

class Env extends FrameworkEnv
{
    public const ADMIN_DATE_FORMAT     = 'ADMIN_DATE_FORMAT';
    public const ADMIN_DATETIME_FORMAT = 'ADMIN_DATETIME_FORMAT';

    public const ADMIN_BASE_PATH   = 'ADMIN_BASE_PATH';
    public const ADMIN_LOGIN_PATH  = 'ADMIN_LOGIN_PATH';
    public const ADMIN_LOGOUT_PATH = 'ADMIN_LOGOUT_PATH';

    public const MEDIA_BASE_URL   = 'MEDIA_BASE_URL';
    public const EDITOR_CLIENT_ID = 'EDITOR_CLIENT_ID';
    public const EDITOR_BASE_PATH = 'EDITOR_BASE_PATH';
}
