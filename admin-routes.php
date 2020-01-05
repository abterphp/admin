<?php

declare(strict_types=1);

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Constant\Route as RouteConstant;
use AbterPhp\Admin\Http\Middleware\Authentication;
use AbterPhp\Admin\Http\Middleware\Authorization;
use AbterPhp\Admin\Http\Middleware\LastGridPage;
use AbterPhp\Framework\Authorization\Constant\Role;
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
                'path'       => RoutesConfig::getAdminBasePath(),
                'middleware' => [
                    Authentication::class,
                ],
            ],
            function (Router $router) {
                $entities = [
                    'user-groups' => 'UserGroup',
                    'users'       => 'User',
                    'api-clients' => 'ApiClient',
                ];

                foreach ($entities as $route => $controllerName) {
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\User::show() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\UserGroup::show() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\ApiClient::show() */
                    $router->get(
                        "/${route}",
                        "Admin\Grid\\${controllerName}@show",
                        [
                            RouteConstant::OPTION_NAME       => "${route}-list",
                            RouteConstant::OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::READ,
                                    ]
                                ),
                                LastGridPage::class,
                            ],
                        ]
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\User::new() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\UserGroup::new() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\ApiClient::new() */
                    $router->get(
                        "/${route}/new",
                        "Admin\Form\\${controllerName}@new",
                        [
                            RouteConstant::OPTION_NAME       => "${route}-new",
                            RouteConstant::OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::WRITE,
                                    ]
                                ),
                            ],
                        ]
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\User::create() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\UserGroup::create() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\ApiClient::create() */
                    $router->post(
                        "/${route}/new",
                        "Admin\Execute\\${controllerName}@create",
                        [
                            RouteConstant::OPTION_NAME       => "${route}-create",
                            RouteConstant::OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::WRITE,
                                    ]
                                ),
                            ],
                        ]
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\User::edit() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\UserGroup::edit() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\ApiClient::edit() */
                    $router->get(
                        "/${route}/:entityId/edit",
                        "Admin\Form\\${controllerName}@edit",
                        [
                            RouteConstant::OPTION_NAME       => "${route}-edit",
                            RouteConstant::OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::WRITE,
                                    ]
                                ),
                            ],
                        ]
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\User::update() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\UserGroup::update() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\ApiClient::update() */
                    $router->put(
                        "/${route}/:entityId/edit",
                        "Admin\Execute\\${controllerName}@update",
                        [
                            RouteConstant::OPTION_NAME       => "${route}-update",
                            RouteConstant::OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::WRITE,
                                    ]
                                ),
                            ],
                        ]
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\User::delete() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\UserGroup::delete() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\ApiClient::delete() */
                    $router->get(
                        "/${route}/:entityId/delete",
                        "Admin\Execute\\${controllerName}@delete",
                        [
                            RouteConstant::OPTION_NAME       => "${route}-delete",
                            RouteConstant::OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::WRITE,
                                    ]
                                ),
                            ],
                        ]
                    );
                }

                /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\Profile::profile() */
                $router->get(
                    RoutesConfig::PROFILE_PATH,
                    'Admin\Form\Profile@profile',
                    [
                        RouteConstant::OPTION_NAME => RouteConstant::PROFILE_EDIT,
                    ]
                );

                /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\Profile::execute() */
                $router->put(
                    RoutesConfig::PROFILE_PATH,
                    'Admin\Execute\Profile@profile',
                    [
                        RouteConstant::OPTION_NAME => RouteConstant::PROFILE_UPDATE,
                    ]
                );

                /** @see \AbterPhp\Admin\Http\Controllers\Admin\Dashboard::showDashboard() */
                $router->get(
                    RoutesConfig::DASHBOARD_PATH,
                    'Admin\Dashboard@showDashboard',
                    [
                        RouteConstant::OPTION_NAME => RouteConstant::DASHBOARD_VIEW,
                    ]
                );
            }
        );
    }
);
