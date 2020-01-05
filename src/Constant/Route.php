<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Constant;

use AbterPhp\Framework\Constant\Route as FrameworkRoute;

class Route extends FrameworkRoute
{
    public const LOGIN_NEW      = 'login-new';
    public const LOGIN_EXECUTE  = 'login-execute';
    public const LOGOUT_EXECUTE = 'logout-execute';

    public const DASHBOARD_VIEW = 'dashboard-view';

    public const PROFILE_EDIT   = 'profile-edit';
    public const PROFILE_UPDATE = 'profile-update';

    public const USERS_LIST   = 'users-list';
    public const USERS_NEW    = 'users-new';
    public const USERS_CREATE = 'users-create';
    public const USERS_EDIT   = 'users-edit';
    public const USERS_UPDATE = 'users-update';
    public const USERS_DELETE = 'users-delete';
    public const USERS_VIEW   = 'users-view';
    public const USERS_BASE   = 'users-base';
    public const USERS_ENTITY = 'users-entity';

    public const USER_GROUPS_LIST   = 'user-groups-list';
    public const USER_GROUPS_NEW    = 'user-groups-new';
    public const USER_GROUPS_CREATE = 'user-groups-create';
    public const USER_GROUPS_EDIT   = 'user-groups-edit';
    public const USER_GROUPS_UPDATE = 'user-groups-update';
    public const USER_GROUPS_DELETE = 'user-groups-delete';
    public const USER_GROUPS_VIEW   = 'user-groups-view';
    public const USER_GROUPS_BASE   = 'user-groups-base';
    public const USER_GROUPS_ENTITY = 'user-groups-entity';

    public const API_CLIENTS_LIST   = 'api-clients-list';
    public const API_CLIENTS_NEW    = 'api-clients-new';
    public const API_CLIENTS_CREATE = 'api-clients-create';
    public const API_CLIENTS_EDIT   = 'api-clients-edit';
    public const API_CLIENTS_UPDATE = 'api-clients-update';
    public const API_CLIENTS_DELETE = 'api-clients-delete';
    public const API_CLIENTS_VIEW   = 'api-clients-view';
    public const API_CLIENTS_BASE   = 'api-clients-base';
    public const API_CLIENTS_ENTITY = 'api-clients-entity';

    public const ACCESS_TOKENS_BASE = 'access-tokens-list';
}
