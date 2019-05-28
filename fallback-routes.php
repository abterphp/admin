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
                'path'       => PATH_API,
                'middleware' => [
                    Api::class,
                ],
            ],
            function (Router $router) {
                /** @see \AbterPhp\Admin\Http\Controllers\Api\Index::notFound() */
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
