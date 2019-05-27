<?php

use AbterPhp\Admin\Bootstrappers;
use AbterPhp\Admin\Console;
use AbterPhp\Admin\Events;
use AbterPhp\Admin\Http;
use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Constant\Module;
use AbterPhp\Framework\Constant\Priorities;

return [
    Module::IDENTIFIER         => 'AbterPhp\Admin',
    Module::DEPENDENCIES       => ['AbterPhp\Framework'],
    Module::ENABLED            => true,
    Module::BOOTSTRAPPERS      => [
        Bootstrappers\Orm\OrmBootstrapper::class,
        Bootstrappers\Validation\ValidatorBootstrapper::class,
    ],
    Module::CLI_BOOTSTRAPPERS  => [
        Bootstrappers\Console\Commands\CommandsBootstrapper::class,
        Bootstrappers\Database\MigrationsBootstrapper::class,
    ],
    Module::HTTP_BOOTSTRAPPERS => [
        Bootstrappers\Http\Controllers\Execute\LoginBootstrapper::class,
        Bootstrappers\Http\Controllers\Form\ApiClientBootstrapper::class,
        Bootstrappers\Http\Controllers\Form\LoginBootstrapper::class,
        Bootstrappers\Http\Controllers\Form\UserBootstrapper::class,
        Bootstrappers\Http\Controllers\Form\ProfileBootstrapper::class,
        Bootstrappers\Http\Views\BuildersBootstrapper::class,
        Bootstrappers\Oauth2\AuthorizationServerBootstrapper::class,
        Bootstrappers\Oauth2\ResourceServerBootstrapper::class,
        Bootstrappers\Vendor\SlugifyBootstrapper::class,
    ],
    Module::COMMANDS           => [
        Console\Commands\User\Create::class,
        Console\Commands\User\Delete::class,
        Console\Commands\User\UpdatePassword::class,
        Console\Commands\UserGroup\Display::class,
    ],
    Module::EVENTS             => [
        Event::AUTH_READY         => [
            /** @see Events\Listeners\AuthInitializer::handle */
            Priorities::NORMAL => [sprintf('%s@handle', Events\Listeners\AuthInitializer::class)],
        ],
        Event::NAVIGATION_READY   => [
            /** @see Events\Listeners\NavigationBuilder::handle */
            Priorities::NORMAL => [sprintf('%s@handle', Events\Listeners\NavigationBuilder::class)],
        ],
        Event::ENTITY_POST_CHANGE => [
            /** @see Events\Listeners\AuthInvalidator::handle */
            Priorities::NORMAL => [sprintf('%s@handle', Events\Listeners\AuthInvalidator::class)],
        ],
        Event::DASHBOARD_READY    => [
            /** @see Events\Listeners\DashboardRegistrar::build */
            Priorities::NORMAL => [sprintf('%s@handle', Events\Listeners\DashboardBuilder::class)],
        ],
    ],
    Module::MIDDLEWARE         => [
        Priorities::NORMAL => [
            Http\Middleware\CheckCsrfToken::class,
            Http\Middleware\Security::class,
        ],
    ],
    Module::ROUTE_PATHS        => [
        Priorities::NORMAL => [
            __DIR__ . '/admin-routes.php',
            __DIR__ . '/api-routes.php',
            __DIR__ . '/login-routes.php',
        ],
    ],
    Module::MIGRATION_PATHS    => [
        Priorities::NORMAL => [
            realpath(__DIR__ . '/src/Databases/Migrations'),
        ],
    ],
    Module::RESOURCE_PATH      => realpath(__DIR__ . '/resources'),
    Module::ASSETS_PATHS       => [
        'admin-assets' => realpath(__DIR__ . '/resources/rawassets'),
    ],
];
