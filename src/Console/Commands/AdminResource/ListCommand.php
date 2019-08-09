<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\AdminResource;

use AbterPhp\Admin\Domain\Entities\AdminResource; // @phan-suppress-current-line PhanUnreferencedUseNormal
use AbterPhp\Admin\Orm\AdminResourceRepo;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class ListCommand extends Command
{
    const COMMAND_NAME        = 'adminresource:list';
    const COMMAND_DESCRIPTION = 'List available admin resources';

    /** @var AdminResourceRepo */
    protected $adminResourceRepo;

    /**
     * ListCommand constructor.
     *
     * @param AdminResourceRepo $adminResourceRepo
     */
    public function __construct(AdminResourceRepo $adminResourceRepo)
    {
        $this->adminResourceRepo = $adminResourceRepo;

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
        /** @var AdminResource[] $adminResources */
        $adminResources = $this->adminResourceRepo->getAll();

        foreach ($adminResources as $adminResource) {
            $response->writeln($adminResource->getIdentifier());
        }
    }
}
