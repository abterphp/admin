<?php

declare(strict_types=1);

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Constant\Routes as RoutesConstant;
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
                    'usergroups' => 'UserGroup',
                    'users'      => 'User',
                    'apiclients' => 'ApiClient',
                ];

                foreach ($entities as $route => $controllerName) {
                    $path = strtolower($controllerName);

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\User::show() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\UserGroup::show() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\ApiClient::show() */
                    $router->get(
                        "/${path}",
                        "Admin\Grid\\${controllerName}@show",
                        [
                            RoutesConstant::OPTION_NAME       => "${route}",
                            RoutesConstant::OPTION_MIDDLEWARE => [
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
                        "/${path}/new",
                        "Admin\Form\\${controllerName}@new",
                        [
                            RoutesConstant::OPTION_NAME       => "${route}-new",
                            RoutesConstant::OPTION_MIDDLEWARE => [
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
                        "/${path}/new",
                        "Admin\Execute\\${controllerName}@create",
                        [
                            RoutesConstant::OPTION_NAME       => "${route}-create",
                            RoutesConstant::OPTION_MIDDLEWARE => [
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
                        "/${path}/:entityId/edit",
                        "Admin\Form\\${controllerName}@edit",
                        [
                            RoutesConstant::OPTION_NAME       => "${route}-edit",
                            RoutesConstant::OPTION_MIDDLEWARE => [
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
                        "/${path}/:entityId/edit",
                        "Admin\Execute\\${controllerName}@update",
                        [
                            RoutesConstant::OPTION_NAME       => "${route}-update",
                            RoutesConstant::OPTION_MIDDLEWARE => [
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
                        "/${path}/:entityId/delete",
                        "Admin\Execute\\${controllerName}@delete",
                        [
                            RoutesConstant::OPTION_NAME       => "${route}-delete",
                            RoutesConstant::OPTION_MIDDLEWARE => [
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
                    RoutesConstant::PATH_PROFILE,
                    'Admin\Form\Profile@profile',
                    [
                        RoutesConstant::OPTION_NAME => RoutesConstant::ROUTE_PROFILE,
                    ]
                );

                /** @see \AbterPhp\Admin\Http\Controllers\Admin\Execute\Profile::execute() */
                $router->put(
                    RoutesConstant::PATH_PROFILE,
                    'Admin\Execute\Profile@profile',
                    [
                        RoutesConstant::OPTION_NAME => RoutesConstant::ROUTE_PROFILE,
                    ]
                );

                /** @see \AbterPhp\Admin\Http\Controllers\Admin\Dashboard::showDashboard() */
                $router->get(
                    RoutesConstant::PATH_DASHBOARD,
                    'Admin\Dashboard@showDashboard',
                    [
                        RoutesConstant::OPTION_NAME => RoutesConstant::ROUTE_DASHBOARD,
                    ]
                );
            }
        );
    }
);
