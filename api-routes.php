<?php

declare(strict_types=1);

use AbterPhp\Admin\Constant\Routes;
use Opulence\Routing\Router;
use AbterPhp\Admin\Http\Middleware\Api;

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
        $router->group(
            [
                'path' => PATH_API,
            ],
            function (Router $router) {
                /** @see \AbterPhp\Admin\Http\Controllers\Api\AccessToken::create() */
                $router->post(
                    Routes::PATH_ACCESS_TOKEN,
                    'Api\AccessToken@create',
                    [
                        OPTION_NAME => Routes::ROUTE_ACCESS_TOKEN,
                    ]
                );
            }
        );
        $router->group(
            [
                'path' => PATH_API,
                'middleware' => [
                    Api::class,
                ],
            ],
            function (Router $router) {
                /** @see \AbterPhp\Admin\Http\Controllers\Api\User::create() */
                $router->post(
                    Routes::PATH_USERS,
                    'Api\User@create'
                );

                /** @see \AbterPhp\Admin\Http\Controllers\Api\User::update() */
                $router->put(
                    Routes::PATH_USERS_ENTITY,
                    'Api\User@update'
                );

                /** @see \AbterPhp\Framework\Http\Controllers\Api\Index::notFound() */
                $router->any(
                    Routes::PATH_404,
                    'Api\Index@notFound',
                    [
                        OPTION_VARS => [Routes::VAR_ANYTHING => '.+'],
                    ]
                );
            }
        );
    }
);
