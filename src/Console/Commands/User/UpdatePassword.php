<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\User;

use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Framework\Authorization\CacheManager;
use AbterPhp\Framework\Crypto\Crypto;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Console\StatusCodes;
use Opulence\Orm\IUnitOfWork;
use ZxcvbnPhp\Zxcvbn;

class UpdatePassword extends Command
{
    public const COMMAND_NAME            = 'user:update-password';
    public const COMMAND_DESCRIPTION     = 'Update the password of an existing user';
    public const COMMAND_SUCCESS         = '<success>User password is updated.</success>';
    public const COMMAND_DRY_RUN_MESSAGE = '<info>Dry run prevented updating user password.</info>';
    public const COMMAND_UNSAFE_PASSWORD = '<fatal>Password provided is not safe.</fatal>';

    public const ARGUMENT_IDENTIFIER = 'identifier';
    public const ARGUMENT_PASSWORD   = 'password';

    public const OPTION_DRY_RUN    = 'dry-run';
    public const SHORTENED_DRY_RUN = 'd';
    public const OPTION_UNSAFE     = 'unsafe';

    protected UserRepo $userRepo;

    protected Crypto $crypto;

    protected IUnitOfWork $unitOfWork;

    protected CacheManager $cacheManager;

    protected Zxcvbn $zxcvbn;

    /**
     * CreateUserCommand constructor.
     *
     * @param UserRepo     $userRepo
     * @param Crypto       $crypto
     * @param IUnitOfWork  $unitOfWork
     * @param CacheManager $cacheManager
     * @param Zxcvbn       $zxcvbn
     */
    public function __construct(
        UserRepo $userRepo,
        Crypto $crypto,
        IUnitOfWork $unitOfWork,
        CacheManager $cacheManager,
        Zxcvbn $zxcvbn
    ) {
        $this->userRepo     = $userRepo;
        $this->crypto       = $crypto;
        $this->unitOfWork   = $unitOfWork;
        $this->cacheManager = $cacheManager;
        $this->zxcvbn       = $zxcvbn;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addArgument(
                new Argument(
                    static::ARGUMENT_IDENTIFIER,
                    ArgumentTypes::REQUIRED,
                    'Identifier (Email or Username)'
                )
            )
            ->addArgument(new Argument(static::ARGUMENT_PASSWORD, ArgumentTypes::REQUIRED, 'Password'))
            ->addOption(
                new Option(
                    static::OPTION_DRY_RUN,
                    static::SHORTENED_DRY_RUN,
                    OptionTypes::OPTIONAL_VALUE,
                    'Dry run (default: 0)',
                    '0'
                )
            )
            ->addOption(
                new Option(
                    static::OPTION_UNSAFE,
                    null,
                    OptionTypes::OPTIONAL_VALUE,
                    'Unsafe (default: 0)',
                    '0'
                )
            );
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $identifier = $this->getArgumentValue(static::ARGUMENT_IDENTIFIER);
        $password   = $this->getArgumentValue(static::ARGUMENT_PASSWORD);
        $dryRun     = $this->getOptionValue(static::OPTION_DRY_RUN);

        if (!$this->isSafe()) {
            $response->writeln(static::COMMAND_UNSAFE_PASSWORD);

            return StatusCodes::ERROR;
        }

        $preparedPassword = $this->crypto->prepareSecret($password);
        $packedPassword   = $this->crypto->hashCrypt($preparedPassword);

        $entity = $this->userRepo->find($identifier);
        if (!$entity) {
            $response->writeln(sprintf('<fatal>User not found</fatal>'));

            return StatusCodes::ERROR;
        }

        $entity->setPassword($packedPassword);

        if ($dryRun) {
            $this->unitOfWork->dispose();
            $response->writeln(static::COMMAND_DRY_RUN_MESSAGE);

            return StatusCodes::OK;
        }

        try {
            $this->unitOfWork->commit();
            $this->cacheManager->clearAll();
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                $response->writeln(sprintf('<error>%s</error>', $e->getPrevious()->getMessage()));
            }
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));

            return StatusCodes::FATAL;
        }

        $response->writeln(static::COMMAND_SUCCESS);

        return StatusCodes::OK;
    }

    /**
     * @return bool
     */
    protected function isSafe(): bool
    {
        $unsafe = $this->getOptionValue(static::OPTION_UNSAFE);
        if ($unsafe) {
            return true;
        }

        $password = (string)$this->getArgumentValue(static::ARGUMENT_PASSWORD);
        $strength = $this->zxcvbn->passwordStrength($password);

        return (int)$strength['score'] >= 4;
    }
}
