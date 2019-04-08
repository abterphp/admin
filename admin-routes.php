<?php

declare(strict_types=1);

use AbterPhp\Admin\Constant\Routes;
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
                'path'       => PATH_ADMIN,
                'middleware' => [
                    Authentication::class,
                ],
            ],
            function (Router $router) {
                $entities = [
                    'usergroups' => 'UserGroup',
                    'users'      => 'User',
                ];

                foreach ($entities as $route => $controllerName) {
                    $path = strtolower($controllerName);

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\User::show() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Grid\UserGroup::show() */
                    $router->get(
                        "/${path}",
                        "Admin\Grid\\${controllerName}@show",
                        [
                            OPTION_NAME       => "${route}",
                            OPTION_MIDDLEWARE => [
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
                    $router->get(
                        "/${path}/new",
                        "Admin\Form\\${controllerName}@new",
                        [
                            OPTION_NAME       => "${route}-new",
                            OPTION_MIDDLEWARE => [
                                Authorization::withParameters(
                                    [
                                        Authorization::RESOURCE => $route,
                                        Authorization::ROLE     => Role::WRITE,
                                    ]
                                ),
                            ],
                        ]
                    );

                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\User::create() */
                    /** @see \AbterPhp\Admin\Http\Controllers\Admin\Form\UserGroup::create() */
                    $router->post(
                        "/${path}/new",
                        "Admin\Execute\\${controllerName}@create",
                        [
                            OPTION_NAME       => "${route}-create",
                            OPTION_MIDDLEWARE => [
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
                    $router->get(
                        "/${path}/:entityId/edit",
                        "Admin\Form\\${controllerName}@edit",
                        [
                            OPTION_NAME       => "${route}-edit",
                            OPTION_MIDDLEWARE => [
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
                    $router->put(
                        "/${path}/:entityId/edit",
                        "Admin\Execute\\${controllerName}@update",
                        [
                            OPTION_NAME       => "${route}-update",
                            OPTION_MIDDLEWARE => [
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
                    $router->get(
                        "/${path}/:entityId/delete",
                        "Admin\Execute\\${controllerName}@delete",
                        [
                            OPTION_NAME       => "${route}-delete",
                            OPTION_MIDDLEWARE => [
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

                /** @see \AbterPhp\Admin\Http\Controllers\Admin\Dashboard::showDashboard() */
                $router->get(
                    Routes::PATH_DASHBOARD,
                    'Admin\Dashboard@showDashboard',
                    [
                        OPTION_NAME => Routes::ROUTE_DASHBOARD,
                    ]
                );
            }
        );
    }
);
