<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Bootstrappers\Service\Execute;

use AbterPhp\Admin\Databases\Queries\LoginThrottle;
use AbterPhp\Admin\Orm\LoginAttemptRepo;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Admin\Service\Login;
use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Crypto\Crypto;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Orm\IUnitOfWork;

class LoginBootstrapper extends Bootstrapper implements ILazyBootstrapper
{
    /**
     * @return array
     */
    public function getBindings(): array
    {
        return [
            Login::class,
        ];
    }

    /**
     * @param IContainer $container
     */
    public function registerBindings(IContainer $container)
    {
        $userRepo         = $container->resolve(UserRepo::class);
        $loginAttemptRepo = $container->resolve(LoginAttemptRepo::class);
        $loginThrottle    = $container->resolve(LoginThrottle::class);
        $crypto           = $container->resolve(Crypto::class);
        $unitOfWork       = $container->resolve(IUnitOfWork::class);

        $loginMaxAttempts = (int)getenv(Env::LOGIN_MAX_ATTEMPTS);
        $loginLogIp       = (bool)getenv(Env::LOGIN_LOG_IP);

        $login = new Login(
            $userRepo,
            $loginAttemptRepo,
            $loginThrottle,
            $crypto,
            $unitOfWork,
            $loginMaxAttempts,
            $loginLogIp
        );

        $container->bindInstance(Login::class, $login);
    }
}
