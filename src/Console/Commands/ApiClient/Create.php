<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\ApiClient;

use AbterPhp\Admin\Domain\Entities\AdminResource;
use AbterPhp\Admin\Domain\Entities\ApiClient;
use AbterPhp\Admin\Orm\AdminResourceRepo;
use AbterPhp\Admin\Orm\ApiClientRepo;
use AbterPhp\Admin\Orm\UserRepo;
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

class Create extends Command
{
    const COMMAND_NAME            = 'apiclient:create';
    const COMMAND_DESCRIPTION     = 'Creates a new API client';
    const COMMAND_SUCCESS         = '<success>New API client is created. ID: <b>%s</b></success>';
    const COMMAND_DRY_RUN_MESSAGE = '<info>Dry run prevented creating new API client.</info>';

    const ARGUMENT_USER        = 'user';
    const ARGUMENT_DESCRIPTION = 'description';
    const ARGUMENT_RESOURCES   = 'resources';

    const OPTION_DRY_RUN    = 'dry-run';
    const SHORTENED_DRY_RUN = 'd';

    const RESPONSE_SECRET = '<info>Secret generated: <b>%s</b></info>';

    /** @var UserRepo */
    protected $userRepo;

    /** @var AdminResourceRepo */
    protected $adminResourceRepo;

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
     * Create constructor.
     *
     * @param UserRepo          $userRepo
     * @param AdminResourceRepo $adminResourceRepo
     * @param ApiClientRepo     $apiClientRepo
     * @param PasswordGenerator $passwordGenerator
     * @param Crypto            $crypto
     * @param IUnitOfWork       $unitOfWork
     * @param CacheManager      $cacheManager
     * @param Zxcvbn            $zxcvbn
     */
    public function __construct(
        UserRepo $userRepo,
        AdminResourceRepo $adminResourceRepo,
        ApiClientRepo $apiClientRepo,
        PasswordGenerator $passwordGenerator,
        Crypto $crypto,
        IUnitOfWork $unitOfWork,
        CacheManager $cacheManager,
        Zxcvbn $zxcvbn
    ) {
        $this->userRepo          = $userRepo;
        $this->adminResourceRepo = $adminResourceRepo;
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
                    static::ARGUMENT_USER,
                    ArgumentTypes::REQUIRED,
                    'User Identifier (Email or Username)'
                )
            )
            ->addArgument(
                new Argument(
                    static::ARGUMENT_DESCRIPTION,
                    ArgumentTypes::REQUIRED,
                    'Description'
                )
            )
            ->addArgument(
                new Argument(
                    static::ARGUMENT_RESOURCES,
                    ArgumentTypes::REQUIRED,
                    'Resources (Comma separated list)'
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
        $userIdentifier = $this->getArgumentValue(static::ARGUMENT_USER);

        try {
            $user = $this->userRepo->find($userIdentifier);
            if (!$user) {
                throw new \RuntimeException();
            }

            $adminResources = $this->getAdminResources($user->getId());

            $rawSecret        = $this->passwordGenerator->generatePassword();
            $preparedPassword = $this->crypto->prepareSecret($rawSecret);
            $packedPassword   = $this->crypto->hashCrypt($preparedPassword);

            $apiClient = $this->getApiClient($user->getId(), $packedPassword, $adminResources);

            $this->apiClientRepo->add($apiClient);
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

        $response->writeln(sprintf(static::RESPONSE_SECRET, $rawSecret));
        $response->writeln(sprintf(static::COMMAND_SUCCESS, $apiClient->getId()));

        return StatusCodes::OK;
    }

    /**
     * @param string          $userId
     * @param string          $packedPassword
     * @param AdminResource[] $adminResources
     *
     * @return ApiClient
     * @throws \Opulence\Orm\OrmException
     * @throws \RuntimeException
     */
    protected function getApiClient(string $userId, string $packedPassword, array $adminResources): ApiClient
    {
        $description = $this->getArgumentValue(static::ARGUMENT_DESCRIPTION);

        return new ApiClient(
            '',
            $userId,
            $description,
            $packedPassword,
            $adminResources
        );
    }

    /**
     * @param string $userId
     *
     * @return AdminResource[]
     * @throws \Opulence\Orm\OrmException
     */
    protected function getAdminResources(string $userId): array
    {
        $resources = explode(',', $this->getArgumentValue(static::ARGUMENT_RESOURCES));
        if (!$resources) {
            return [];
        }

        $adminResources = [];
        foreach ($this->adminResourceRepo->getByUserId($userId) as $adminResource) {
            if (!in_array($adminResource->getIdentifier(), $resources, true)) {
                continue;
            }

            $adminResources[] = $adminResource;
        }

        if (count($resources) != count($adminResources)) {
            throw new \RuntimeException('User does not have all requested resources');
        }

        return $adminResources;
    }
}
