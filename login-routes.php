<?php

declare(strict_types=1);

use AbterPhp\Admin\Constant\Routes;
use Opulence\Routing\Router;

/**
 * ----------------------------------------------------------
 * Create all of the routes for the HTTP kernel
 * ----------------------------------------------------------
 *
 * @var Router $router
 */
$router->group(
    ['controllerNamespace' => 'AbterPhp\Admin\\Http\\Controllers'],
    function (Router $router) {
        /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\Login::display() */
        $router->get(PATH_LOGIN, 'Admin\Form\Login@display', [OPTION_NAME => Routes::ROUTE_LOGIN]);
        /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\Login::execute() */
        $router->post(PATH_LOGIN, 'Admin\Execute\Login@execute', [OPTION_NAME => Routes::ROUTE_LOGIN_POST]);
        /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\Logout::execute() */
        $router->get(Routes::PATH_LOGOUT, 'Admin\Execute\Logout@execute', [OPTION_NAME => Routes::ROUTE_LOGOUT]);
    }
);
