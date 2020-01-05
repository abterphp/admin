<?php

declare(strict_types=1);

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Constant\Route as RouteConstant;
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
        $router->get(
            RoutesConfig::getLoginPath(),
            'Admin\Form\Login@display',
            [RouteConstant::OPTION_NAME => RouteConstant::LOGIN_NEW]
        );

        /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\Login::execute() */
        $router->post(
            RoutesConfig::getLoginPath(),
            'Admin\Execute\Login@execute',
            [RouteConstant::OPTION_NAME => RouteConstant::LOGIN_EXECUTE]
        );

        /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\Logout::execute() */
        $router->get(
            RoutesConfig::getLogoutPath(),
            'Admin\Execute\Logout@execute',
            [RouteConstant::OPTION_NAME => RouteConstant::LOGOUT_EXECUTE]
        );
    }
);
