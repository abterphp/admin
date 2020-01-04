<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Constant;

use AbterPhp\Framework\Constant\Routes as FrameworkRoutes;

class Routes extends FrameworkRoutes
{
    const ROUTE_403 = '403';
    const PATH_403  = '/nope';

    const ROUTE_LOGIN      = 'login';
    const ROUTE_LOGIN_POST = 'login-post';
    const ROUTE_LOGOUT     = 'logout';
    const ROUTE_DASHBOARD  = 'dashboard';

    const PATH_LOGOUT    = '/logout';
    const PATH_DASHBOARD = '/dashboard';

    const ROUTE_API_CLIENTS        = 'api-clients';
    const ROUTE_API_CLIENTS_NEW    = 'api-clients-new';
    const ROUTE_API_CLIENTS_EDIT   = 'api-clients-edit';
    const ROUTE_API_CLIENTS_DELETE = 'api-clients-delete';
    const PATH_API_CLIENTS         = '/api-clients';
    const PATH_API_CLIENTS_NEW     = '/api-clients/new';
    const PATH_API_CLIENTS_EDIT    = '/api-clients/:entityId/edit';
    const PATH_API_CLIENTS_DELETE  = '/api-clients/:entityId/delete';

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

    const ROUTE_USER_GROUPS        = 'user-groups';
    const ROUTE_USER_GROUPS_NEW    = 'user-groups-new';
    const ROUTE_USER_GROUPS_EDIT   = 'user-groups-edit';
    const ROUTE_USER_GROUPS_DELETE = 'user-groups-delete';
    const PATH_USER_GROUPS         = '/user-groups';
    const PATH_USER_GROUPS_NEW     = '/user-groups/new';
    const PATH_USER_GROUPS_EDIT    = '/user-groups/:entityId/edit';
    const PATH_USER_GROUPS_DELETE  = '/user-groups/:entityId/delete';
    const PATH_USER_GROUPS_ENTITY  = '/user-groups/:entityId';

    const ROUTE_ACCESS_TOKEN = 'access-tokens';
    const PATH_ACCESS_TOKEN  = '/access-tokens';

    const ROUTE_404 = '404';
    const PATH_404  = '/:anything';

    const VAR_ANYTHING = 'anything';
}
