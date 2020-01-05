<?php

declare(strict_types=1);

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Constant\Route as RouteConstant;
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
                'path' => RoutesConfig::getApiBasePath(),
            ],
            function (Router $router) {
                /** @see \AbterPhp\Admin\Http\Controllers\Api\AccessToken::create() */
                $router->post(
                    '/access-tokens',
                    'Api\AccessToken@create',
                    [
                        RouteConstant::OPTION_NAME => RouteConstant::ACCESS_TOKENS_BASE,
                    ]
                );
            }
        );
        $router->group(
            [
                'path'       => RoutesConfig::getApiBasePath(),
                'middleware' => [
                    Api::class,
                ],
            ],
            function (Router $router) {
                $entities = [
                    'user-groups'    => 'UserGroup',
                    'user-languages' => 'UserLanguage',
                    'users'          => 'User',
                    'apic-lients'    => 'ApiClient',
                ];

                foreach ($entities as $route => $controllerName) {
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserLanguage::get() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserGroup::get() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\User::get() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\ApiClient::get() */
                    $router->get(
                        "/${route}/:entityId",
                        "Api\\${controllerName}@get"
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserLanguage::list() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\UserGroup::list() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\User::list() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Api\ApiClient::list() */
                    $router->get(
                        "/${route}",
                        "Api\\${controllerName}@list"
                    );

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
