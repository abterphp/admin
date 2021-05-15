<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\UserGroup;

use AbterPhp\Admin\Domain\Entities\UserGroup; // @phan-suppress-current-line PhanUnreferencedUseNormal
use AbterPhp\Admin\Orm\UserGroupRepo;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

// @phan-suppress-current-line PhanUnreferencedUseNormal
class ListCommand extends Command
{
    public const COMMAND_NAME        = 'usergroup:list';
    public const COMMAND_DESCRIPTION = 'List available user groups';

    protected UserGroupRepo $userGroupRepo;

    /**
     * ListCommand constructor.
     *
     * @param UserGroupRepo $userGroupRepo
     */
    public function __construct(UserGroupRepo $userGroupRepo)
    {
        $this->userGroupRepo = $userGroupRepo;

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
        /** @var UserGroup[] $userGroups */
        $userGroups = $this->userGroupRepo->getAll();

        foreach ($userGroups as $userGroup) {
            $response->writeln($userGroup->getIdentifier());
        }
    }
}
