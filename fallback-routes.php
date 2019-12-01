<?php

declare(strict_types=1);

use AbterPhp\Admin\Config\Routes as RoutesConfig;
use AbterPhp\Admin\Constant\Routes as RoutesConstant;
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
                'path'       => RoutesConfig::getApiBasePath(),
                'middleware' => [
                    Api::class,
                ],
            ],
            function (Router $router) {
                /** @see \AbterPhp\Admin\Http\Controllers\Api\Index::notFound() */
                $router->any(
                    RoutesConstant::PATH_404,
                    'Api\Index@notFound',
                    [
                        RoutesConstant::OPTION_VARS => [RoutesConstant::VAR_ANYTHING => '.+'],
                    ]
                );
            }
        );
    }
);
