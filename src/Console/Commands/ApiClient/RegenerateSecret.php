<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\ApiClient;

use AbterPhp\Admin\Orm\ApiClientRepo;
use AbterPhp\Framework\Authorization\CacheManager;
use AbterPhp\Framework\Crypto\Crypto;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator as PasswordGenerator;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Console\StatusCodes;
use Opulence\Orm\IUnitOfWork;
use ZxcvbnPhp\Zxcvbn;

class RegenerateSecret extends Command
{
    const COMMAND_NAME            = 'apiclient:regenerate-secret';
    const COMMAND_DESCRIPTION     = 'Regenerate the secret of an existing API client';
    const COMMAND_SUCCESS         = '<success>API client secret is updated.</success>';
    const COMMAND_DRY_RUN_MESSAGE = '<info>Dry run prevented updating existing API client.</info>';

    const ARGUMENT_IDENTIFIER = 'identifier';

    const OPTION_DRY_RUN    = 'dry-run';
    const SHORTENED_DRY_RUN = 'd';

    const RESPONSE_SECRET = '<info>Secret generated: <b>%s</b></info>';

    /** @var ApiClientRepo */
    protected $apiClientRepo;

    /** @var PasswordGenerator */
    protected $passwordGenerator;

    /** @var Crypto */
    protected $crypto;

    /** @var IUnitOfWork */
    protected $unitOfWork;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var Zxcvbn */
    protected $zxcvbn;

    /**
     * RegenerateSecret constructor.
     *
     * @param ApiClientRepo     $apiClientRepo
     * @param PasswordGenerator $passwordGenerator
     * @param Crypto            $crypto
     * @param IUnitOfWork       $unitOfWork
     * @param CacheManager      $cacheManager
     * @param Zxcvbn            $zxcvbn
     */
    public function __construct(
        ApiClientRepo $apiClientRepo,
        PasswordGenerator $passwordGenerator,
        Crypto $crypto,
        IUnitOfWork $unitOfWork,
        CacheManager $cacheManager,
        Zxcvbn $zxcvbn
    ) {
        $this->apiClientRepo     = $apiClientRepo;
        $this->passwordGenerator = $passwordGenerator;
        $this->crypto            = $crypto;
        $this->unitOfWork        = $unitOfWork;
        $this->cacheManager      = $cacheManager;
        $this->zxcvbn            = $zxcvbn;

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
                    'Identifier'
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

        try {
            $apiClient = $this->apiClientRepo->getById($identifier);
            if (!$apiClient) {
                $response->writeln(sprintf('<fatal>API client not found</fatal>'));

                return StatusCodes::ERROR;
            }

            $rawSecret        = $this->passwordGenerator->generatePassword();
            $preparedPassword = $this->crypto->prepareSecret($rawSecret);
            $packedPassword   = $this->crypto->hashCrypt($preparedPassword);

            $apiClient->setSecret($packedPassword);
        } catch (\Exception $e) {
            if ($e->getPrevious()) {
                $response->writeln(sprintf('<error>%s</error>', $e->getPrevious()->getMessage()));
            }
            $response->writeln(sprintf('<fatal>%s</fatal>', $e->getMessage()));

            return StatusCodes::FATAL;
        }

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

        $response->writeln(sprintf(static::RESPONSE_SECRET, $rawSecret));
        $response->writeln(static::COMMAND_SUCCESS);

        return StatusCodes::OK;
    }
}
