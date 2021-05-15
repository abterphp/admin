<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Validation;

use AbterPhp\Admin\Validation\Factory\User;
use AbterPhp\Admin\Validation\Factory\UserGroup;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Validation\Rules\Base64;
use AbterPhp\Framework\Validation\Rules\ExactlyOne;
use AbterPhp\Framework\Validation\Rules\Forbidden;
use AbterPhp\Framework\Validation\Rules\MaxLength;
use AbterPhp\Framework\Validation\Rules\MinLength;
use AbterPhp\Framework\Validation\Rules\Url;
use AbterPhp\Framework\Validation\Rules\Uuid;
use InvalidArgumentException;
use Opulence\Framework\Configuration\Config;
use Opulence\Framework\Validation\Bootstrappers\ValidatorBootstrapper as BaseBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;
use Opulence\Validation\Rules\RuleExtensionRegistry;

/**
 * Defines the validator bootstrapper
 */
class ValidatorBootstrapper extends BaseBootstrapper
{
    protected const LANG_PATH = 'lang/';

    /**
     * @var string[]
     */
    protected array $validatorFactories = [
        User::class,
        UserGroup::class,
    ];

    /**
     * @return string[]
     */
    public function getBindings(): array
    {
        return array_merge(
            parent::getBindings(),
            $this->validatorFactories
        );
    }

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        parent::registerBindings($container);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     *
     * Registers the error templates
     *
     * @param ErrorTemplateRegistry $errorTemplateRegistry The registry to register to
     *
     * @throws InvalidArgumentException Thrown if the config was invalid
     */
    protected function registerErrorTemplates(ErrorTemplateRegistry $errorTemplateRegistry)
    {
        global $abterModuleManager;

        $config = [];

        $lang = getenv(Env::DEFAULT_LANGUAGE);

        $path = sprintf('%s/%s/validation.php', Config::get('paths', 'resources.lang'), $lang);
        if (is_file($path)) {
            $config = require $path;
        }

        foreach ($abterModuleManager->getResourcePaths() as $path) {
            $path = sprintf('%s/%s/%s/validation.php', $path, static::LANG_PATH, $lang);
            if (is_file($path)) {
                $config = array_merge($config, require $path);
            }
        }

        $errorTemplateRegistry->registerErrorTemplatesFromConfig($config);
    }

    /**
     * Registers any custom rule extensions
     *
     * @param RuleExtensionRegistry $ruleExtensionRegistry The registry to register rules to
     */
    protected function registerRuleExtensions(RuleExtensionRegistry $ruleExtensionRegistry)
    {
        $ruleExtensionRegistry->registerRuleExtension(new Forbidden());
        $ruleExtensionRegistry->registerRuleExtension(new ExactlyOne());
        $ruleExtensionRegistry->registerRuleExtension(new Base64());
        $ruleExtensionRegistry->registerRuleExtension(new MaxLength());
        $ruleExtensionRegistry->registerRuleExtension(new MinLength());
        $ruleExtensionRegistry->registerRuleExtension(new Url());
        $ruleExtensionRegistry->registerRuleExtension(new Uuid());
    }
}
