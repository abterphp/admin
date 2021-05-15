<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Console\Commands;

use Exception;
use Opulence\Console\Commands\CommandCollection;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use RuntimeException;

/**
 * Defines the command bootstrapper
 */
class CommandsBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     * @throws IocException
     */
    public function registerBindings(IContainer $container)
    {
        global $abterModuleManager;

        $commands = $container->resolve(CommandCollection::class);
        $globalCommandClasses = require Config::get('paths', 'config.console') . '/commands.php';

        $abterCommandClasses = $abterModuleManager->getCommands();

        $commandClasses = array_merge($globalCommandClasses, $abterCommandClasses);

        try {
            // Instantiate each command class
            foreach ($commandClasses as $commandClass) {
                $commands->add($container->resolve($commandClass));
            }
        } catch (Exception $ex) {
            throw new RuntimeException('Failed to add console commands', 0, $ex);
        }
    }
}
