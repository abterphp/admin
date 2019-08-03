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

    const ROUTE_API_CLIENTS        = 'apiclients';
    const ROUTE_API_CLIENTS_NEW    = 'apiclients-new';
    const ROUTE_API_CLIENTS_EDIT   = 'apiclients-edit';
    const ROUTE_API_CLIENTS_DELETE = 'apiclients-delete';
    const PATH_API_CLIENTS         = '/apiclients';
    const PATH_API_CLIENTS_NEW     = '/apiclients/new';
    const PATH_API_CLIENTS_EDIT    = '/apiclients/:entityId/edit';
    const PATH_API_CLIENTS_DELETE  = '/apiclients/:entityId/delete';

    const ROUTE_PROFILE = 'profile';
    const PATH_PROFILE  = '/profile';

    const ROUTE_USERS        = 'users';
    const ROUTE_USERS_NEW    = 'users-new';
    const ROUTE_USERS_EDIT   = 'users-edit';
    const ROUTE_USERS_DELETE = 'users-delete';
    const PATH_USERS         = '/users';
    const PATH_USERS_NEW     = '/users/new';
    const PATH_USERS_EDIT    = '/users/:entityId/edit';
    const PATH_USERS_DELETE  = '/users/:entityId/delete';
    const PATH_USERS_ENTITY  = '/users/:entityId';

    const ROUTE_USER_GROUPS        = 'usergroups';
    const ROUTE_USER_GROUPS_NEW    = 'usergroups-new';
    const ROUTE_USER_GROUPS_EDIT   = 'usergroups-edit';
    const ROUTE_USER_GROUPS_DELETE = 'usergroups-delete';
    const PATH_USER_GROUPS         = '/usergroups';
    const PATH_USER_GROUPS_NEW     = '/usergroups/new';
    const PATH_USER_GROUPS_EDIT    = '/usergroups/:entityId/edit';
    const PATH_USER_GROUPS_DELETE  = '/usergroups/:entityId/delete';
    const PATH_USER_GROUPS_ENTITY  = '/users/:entityId';

    const ROUTE_ACCESS_TOKEN = 'accesstokens';
    const PATH_ACCESS_TOKEN  = '/access-tokens';

    const ROUTE_404 = '404';
    const PATH_404  = '/:anything';

    const VAR_ANYTHING = 'anything';
}
