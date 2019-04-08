<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Constant;

class Routes
{
    const ROUTE_403 = '403';
    const PATH_403  = '/nope';

    const ROUTE_LOGIN      = 'login';
    const ROUTE_LOGIN_POST = 'login-post';
    const ROUTE_LOGOUT     = 'logout';
    const ROUTE_DASHBOARD  = 'dashboard';

    const PATH_LOGOUT    = '/logout';
    const PATH_DASHBOARD = '/dashboard';

    const ROUTE_USERS        = 'users';
    const ROUTE_USERS_NEW    = 'users-new';
    const ROUTE_USERS_EDIT   = 'users-edit';
    const ROUTE_USERS_DELETE = 'users-delete';
    const PATH_USERS         = '/user';
    const PATH_USERS_NEW     = '/user/new';
    const PATH_USERS_EDIT    = '/user/:id/edit';
    const PATH_USERS_DELETE  = '/user/:id/delete';

    const ROUTE_USER_GROUPS        = 'usergroups';
    const ROUTE_USER_GROUPS_NEW    = 'usergroups-new';
    const ROUTE_USER_GROUPS_EDIT   = 'usergroups-edit';
    const ROUTE_USER_GROUPS_DELETE = 'usergroups-delete';
    const PATH_USER_GROUPS         = '/usergroup';
    const PATH_USER_GROUPS_NEW     = '/usergroup/new';
    const PATH_USER_GROUPS_EDIT    = '/usergroup/:id/edit';
    const PATH_USER_GROUPS_DELETE  = '/usergroup/:id/delete';
}
