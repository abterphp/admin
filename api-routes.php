<?php

declare(strict_types=1);

use AbterPhp\Admin\Constant\Routes;
use AbterPhp\Admin\Http\Middleware\Api;
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
                'path'       => PATH_API,
                'middleware' => [
                    Api::class,
                ],
            ],
            function (Router $router) {
                $entities = [
                    'usergroups'    => 'UserGroup',
                    'userlanguages' => 'UserLanguage',
                    'users'         => 'User',
                    'apiclients'    => 'ApiClient',
                ];

                foreach ($entities as $route => $controllerName) {
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserLanguage::create() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserGroup::create() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\User::create() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\ApiClient::create() */
                    $router->post(
                        "/${route}",
                        "Api\\${controllerName}@create"
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserLanguage::update() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserGroup::update() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\User::update() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\ApiClient::update() */
                    $router->put(
                        "/${route}/:entityId",
                        "Api\\${controllerName}@update"
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserLanguage::delete() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserGroup::delete() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\User::delete() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\ApiClient::delete() */
                    $router->delete(
                        "/${route}/:entityId",
                        "Api\\${controllerName}@delete"
                    );
                }
            }
        );
    }
);
