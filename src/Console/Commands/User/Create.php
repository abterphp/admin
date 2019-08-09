<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\User;

use AbterPhp\Admin\Domain\Entities\User;
use AbterPhp\Admin\Orm\UserGroupRepo;
use AbterPhp\Admin\Orm\UserLanguageRepo;
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

class Create extends Command
{
    const COMMAND_NAME            = 'user:create';
    const COMMAND_DESCRIPTION     = 'Creates a new user';
    const COMMAND_SUCCESS         = '<success>New user is created.</success>';
    const COMMAND_DRY_RUN_MESSAGE = '<info>Dry run prevented creating new user.</info>';
    const COMMAND_UNSAFE_PASSWORD = '<fatal>Password provided is not safe.</fatal>';

    const ARGUMENT_USERNAME    = 'username';
    const ARGUMENT_EMAIL       = 'email';
    const ARGUMENT_PASSWORD    = 'password';
    const ARGUMENT_USER_GROUPS = 'usergroups';
    const ARGUMENT_USER_LANG   = 'lang';

    const OPTION_CAN_LOGIN       = 'can-login';
    const SHORTENED_CAN_LOGIN    = 'l';
    const OPTION_HAS_GRAVATAR    = 'has-gravatar';
    const SHORTENED_HAS_GRAVATAR = 'g';
    const OPTION_DRY_RUN         = 'dry-run';
    const SHORTENED_DRY_RUN      = 'd';
    const OPTION_UNSAFE          = 'unsafe';

    /** @var UserRepo */
    protected $userRepo;

    /** @var UserGroupRepo */
    protected $userGroupRepo;

    /** @var UserLanguageRepo */
    protected $userLanguageRepo;

    /** @var Crypto */
    protected $crypto;

    /** @var IUnitOfWork */
    protected $unitOfWork;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var Zxcvbn */
    protected $zxcvbn;

    /**
     * CreateCommand constructor.
     *
     * @param UserRepo         $userRepo
     * @param UserGroupRepo    $userGroupRepo
     * @param UserLanguageRepo $userLanguageRepo
     * @param Crypto           $crypto
     * @param IUnitOfWork      $unitOfWork
     * @param CacheManager     $cacheManager
     * @param Zxcvbn           $zxcvbn
     */
    public function __construct(
        UserRepo $userRepo,
        UserGroupRepo $userGroupRepo,
        UserLanguageRepo $userLanguageRepo,
        Crypto $crypto,
        IUnitOfWork $unitOfWork,
        CacheManager $cacheManager,
        Zxcvbn $zxcvbn
    ) {
        $this->userRepo         = $userRepo;
        $this->userGroupRepo    = $userGroupRepo;
        $this->userLanguageRepo = $userLanguageRepo;
        $this->crypto           = $crypto;
        $this->unitOfWork       = $unitOfWork;
        $this->cacheManager     = $cacheManager;
        $this->zxcvbn           = $zxcvbn;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addArgument(new Argument(static::ARGUMENT_USERNAME, ArgumentTypes::REQUIRED, 'Username'))
            ->addArgument(new Argument(static::ARGUMENT_EMAIL, ArgumentTypes::REQUIRED, 'Email'))
            ->addArgument(new Argument(static::ARGUMENT_PASSWORD, ArgumentTypes::REQUIRED, 'Password'))
            ->addArgument(
                new Argument(
                    static::ARGUMENT_USER_GROUPS,
                    ArgumentTypes::REQUIRED,
                    'User Groups (comma separated list)'
                )
            )
            ->addArgument(new Argument(static::ARGUMENT_USER_LANG, ArgumentTypes::OPTIONAL, 'Language', 'en'))
            ->addOption(
                new Option(
                    static::OPTION_CAN_LOGIN,
                    static::SHORTENED_CAN_LOGIN,
                    OptionTypes::OPTIONAL_VALUE,
                    'Can user log in',
                    '1'
                )
            )
            ->addOption(
                new Option(
                    static::OPTION_HAS_GRAVATAR,
                    static::SHORTENED_HAS_GRAVATAR,
                    OptionTypes::OPTIONAL_VALUE,
                    'Does user have gravatar (https://en.gravatar.com/)',
                    '1'
                )
            )
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
        if (!$this->isSafe()) {
            $response->writeln(static::COMMAND_UNSAFE_PASSWORD);

            return StatusCodes::ERROR;
        }

        try {
            $entity = $this->getEntity();

            $password         = (string)$this->getArgumentValue(static::ARGUMENT_PASSWORD);
            $preparedPassword = $this->crypto->prepareSecret($password);
            $packedPassword   = $this->crypto->hashCrypt($preparedPassword);

            $entity->setPassword($packedPassword);
            $this->userRepo->add($entity);
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                $response->writeln(sprintf('<error>%s</error>', $e->getPrevious()->getMessage()));
            }
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));

            return StatusCodes::FATAL;
        }

        $dryRun = (bool)$this->getOptionValue(static::OPTION_DRY_RUN);
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
     * @return User
     * @throws \Opulence\Orm\OrmException
     * @throws \RuntimeException
     */
    protected function getEntity(): User
    {
        $username      = $this->getArgumentValue(static::ARGUMENT_USERNAME);
        $email         = $this->getArgumentValue(static::ARGUMENT_EMAIL);
        $ugIdentifiers = $this->getArgumentValue(static::ARGUMENT_USER_GROUPS);
        $ulIdentifier  = $this->getArgumentValue(static::ARGUMENT_USER_LANG);
        $canLogin      = (bool)$this->getArgumentValue(static::ARGUMENT_USER_LANG);
        $hasGravatar   = (bool)$this->getArgumentValue(static::ARGUMENT_USER_LANG);

        $userGroups = [];
        foreach (explode(',', $ugIdentifiers) as $ugIdentifier) {
            $userGroups[] = $this->userGroupRepo->getByIdentifier($ugIdentifier);
        }
        $userLanguage = $this->userLanguageRepo->getByIdentifier($ulIdentifier);

        if (!$userLanguage) {
            throw new \RuntimeException('Language not found');
        }

        return new User(
            '',
            $username,
            $email,
            '',
            $canLogin,
            $hasGravatar,
            $userLanguage,
            $userGroups
        );
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
