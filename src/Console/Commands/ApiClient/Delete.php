<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\ApiClient;

use AbterPhp\Admin\Orm\ApiClientRepo;
use AbterPhp\Framework\Authorization\CacheManager;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Console\StatusCodes;
use Opulence\Orm\IUnitOfWork;

class Delete extends Command
{
    const COMMAND_NAME            = 'apiclient:delete';
    const COMMAND_DESCRIPTION     = 'Deletes an existing user';
    const COMMAND_SUCCESS         = '<success>Existing API client is deleted.</success>';
    const COMMAND_DRY_RUN_MESSAGE = '<info>Dry run prevented deleting existing user.</info>';

    const ARGUMENT_IDENTIFIER = 'identifier';

    const OPTION_DRY_RUN    = 'dry-run';
    const SHORTENED_DRY_RUN = 'd';

    /** @var ApiClientRepo */
    protected $apiClientRepo;

    /** @var IUnitOfWork */
    protected $unitOfWork;

    /** @var CacheManager */
    protected $cacheManager;

    /**
     * Delete constructor.
     *
     * @param ApiClientRepo $apiClientRepo
     * @param IUnitOfWork   $unitOfWork
     * @param CacheManager  $cacheManager
     */
    public function __construct(ApiClientRepo $apiClientRepo, IUnitOfWork $unitOfWork, CacheManager $cacheManager)
    {
        $this->apiClientRepo = $apiClientRepo;
        $this->unitOfWork    = $unitOfWork;
        $this->cacheManager  = $cacheManager;

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
                    'Secret ID'
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

        $entity = $this->apiClientRepo->getById($identifier);
        if (!$entity) {
            $response->writeln(sprintf('<fatal>API client not found</fatal>'));

            return StatusCodes::ERROR;
        }

        $this->apiClientRepo->delete($entity);

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
}
