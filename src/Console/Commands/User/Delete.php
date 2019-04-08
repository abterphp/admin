<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\User;

use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Framework\Authorization\CacheManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Orm\IUnitOfWork;

class Delete extends Command
{
    const COMMAND_NAME            = 'user:delete';
    const COMMAND_DESCRIPTION     = 'Deletes an existing user';
    const COMMAND_SUCCESS         = '<success>Existing user is deleted.</success>';
    const COMMAND_DRY_RUN_MESSAGE = '<info>Dry run prevented deleting existing user.</info>';

    const ARGUMENT_IDENTIFIER = 'identifier';

    const OPTION_DRY_RUN    = 'dry-run';
    const SHORTENED_DRY_RUN = 'd';

    /** @var UserRepo */
    protected $userRepo;

    /** @var IUnitOfWork */
    protected $unitOfWork;

    /** @var CacheManager */
    protected $cacheManager;

    /**
     * CreateUserCommand constructor.
     *
     * @param UserRepo     $userRepo
     * @param IUnitOfWork  $unitOfWork
     * @param CacheManager $cacheManager
     */
    public function __construct(UserRepo $userRepo, IUnitOfWork $unitOfWork, CacheManager $cacheManager)
    {
        $this->userRepo     = $userRepo;
        $this->unitOfWork   = $unitOfWork;
        $this->cacheManager = $cacheManager;

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
            ->addOption(
                new Option(
                    static::OPTION_DRY_RUN,
                    static::SHORTENED_DRY_RUN,
                    OptionTypes::OPTIONAL_VALUE,
                    'Dry run (default: 0)',
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
        $dryRun     = $this->getOptionValue(static::OPTION_DRY_RUN);

        $entity = $this->userRepo->find($identifier);
        if (!$entity) {
            $response->writeln(sprintf('<fatal>User not found</fatal>'));

            return;
        }

        $this->userRepo->delete($entity);

        if ($dryRun) {
            $this->unitOfWork->dispose();
            $response->writeln(static::COMMAND_DRY_RUN_MESSAGE);

            return;
        }

        try {
            $this->unitOfWork->commit();
            $this->cacheManager->clearAll();
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                $response->writeln(sprintf('<error>%s</error>', $e->getPrevious()->getMessage()));
            }
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));

            return;
        }

        $response->writeln(static::COMMAND_SUCCESS);
    }
}
