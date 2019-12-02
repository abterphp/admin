<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Console\Commands\User;

use AbterPhp\Admin\Domain\Entities\User; // @phan-suppress-current-line PhanUnreferencedUseNormal
use AbterPhp\Admin\Orm\UserRepo;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\Formatters\TableFormatter;
use Opulence\Console\Responses\IResponse;

class ListCommand extends Command
{
    const COMMAND_NAME        = 'user:list';
    const COMMAND_DESCRIPTION = 'List available users';

    /** @var UserRepo */
    protected $userRepo;

    /**
     * ListCommand constructor.
     *
     * @param UserRepo $userRepo
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;

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
        $header = ['Id', 'Username', 'Email', 'Language', 'Groups'];
        $rows   = [];
        /** @var User $user */
        foreach ($this->userRepo->getAll() as $user) {
            $userGroups = [];
            foreach ($user->getUserGroups() as $ug) {
                $userGroups[] = $ug->getName();
            }

            $rows[] = [
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getUserLanguage()->getIdentifier(),
                join(", ", $userGroups),
            ];
        }

        $table = new TableFormatter(new PaddingFormatter());
        $response->writeln($table->format($rows, $header));
    }
}
