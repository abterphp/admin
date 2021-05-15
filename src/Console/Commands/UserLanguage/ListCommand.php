<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\UserLanguage;

use AbterPhp\Admin\Domain\Entities\UserLanguage; // @phan-suppress-current-line PhanUnreferencedUseNormal
use AbterPhp\Admin\Orm\UserLanguageRepo;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class ListCommand extends Command
{
    public const COMMAND_NAME        = 'userlanguage:list';
    public const COMMAND_DESCRIPTION = 'List available user languages';

    protected UserLanguageRepo $userLanguageRepo;

    /**
     * ListCommand constructor.
     *
     * @param UserLanguageRepo $userLanguageRepo
     */
    public function __construct(UserLanguageRepo $userLanguageRepo)
    {
        $this->userLanguageRepo = $userLanguageRepo;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName(static::COMMAND_NAME)->setDescription(static::COMMAND_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        /** @var UserLanguage[] $userLanguages */
        $userLanguages = $this->userLanguageRepo->getAll();

        foreach ($userLanguages as $userLanguage) {
            $response->writeln($userLanguage->getIdentifier());
        }
    }
}
