<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Service;

use AbterPhp\Admin\Databases\Queries\LoginThrottle;
use AbterPhp\Admin\Domain\Entities\LoginAttempt;
use AbterPhp\Admin\Domain\Entities\User as Entity;
use AbterPhp\Admin\Orm\LoginAttemptRepo;
use AbterPhp\Admin\Orm\UserRepo;
use AbterPhp\Framework\Crypto\Crypto;
use Opulence\Orm\IUnitOfWork;
use Opulence\Orm\OrmException;
use Opulence\QueryBuilders\InvalidQueryException;

class Login
{
    const ERROR_MSG_LOGIN_THROTTLED    = 'login:throttled';
    const ERROR_MSG_DB_PROBLEM         = 'login:dbProblem';
    const ERROR_MSG_UNEXPECTED_PROBLEM = 'login:unexpectedProblem';
    const ERROR_MSG_LOGIN_FAILED       = 'login:unknownFailure';

    /** @var UserRepo */
    protected $userRepo;

    /** @var LoginAttemptRepo */
    protected $loginAttemptRepo;

    /** @var LoginThrottle */
    protected $loginThrottle;

    /** @var Crypto */
    protected $crypto;

    /** @var IUnitOfWork */
    protected $unitOfWork;

    /** @var int */
    protected $loginMaxAttempts;

    /** @var bool */
    protected $loginLogIp;

    /**
     * Login constructor.
     *
     * @param UserRepo         $userRepo
     * @param LoginAttemptRepo $loginAttemptRepo
     * @param LoginThrottle    $loginThrottle
     * @param Crypto           $crypto
     * @param IUnitOfWork      $unitOfWork
     * @param int              $loginMaxAttempts
     * @param bool             $loginLogIp
     */
    public function __construct(
        UserRepo $userRepo,
        LoginAttemptRepo $loginAttemptRepo,
        LoginThrottle $loginThrottle,
        Crypto $crypto,
        IUnitOfWork $unitOfWork,
        int $loginMaxAttempts,
        bool $loginLogIp
    ) {
        $this->userRepo         = $userRepo;
        $this->loginAttemptRepo = $loginAttemptRepo;
        $this->loginThrottle    = $loginThrottle;
        $this->crypto           = $crypto;
        $this->unitOfWork       = $unitOfWork;
        $this->loginMaxAttempts = $loginMaxAttempts;
        $this->loginLogIp       = $loginLogIp;
    }

    /**
     * @param string $username
     * @param string $ipAddress
     *
     * @return bool
     * @throws InvalidQueryException
     * @throws OrmException
     */
    public function isLoginAllowed(string $username, string $ipAddress): bool
    {
        $ipHash = $this->getHash($ipAddress);

        return $this->loginThrottle->isLoginAllowed($ipHash, $username, $this->loginMaxAttempts);
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $ipAddress
     *
     * @return Entity|null
     * @throws InvalidQueryException
     * @throws OrmException
     */
    public function login(string $username, string $password, string $ipAddress): ?Entity
    {
        $ipHash = $this->getHash($ipAddress);

        $user = $this->userRepo->find($username);
        if (!$user || !$user->getPassword()) {
            return null;
        }

        $result = $this->crypto->verifySecret($password, $user->getPassword());
        if (!$result) {
            return null;
        }

        if ($user && $this->loginThrottle->clear($ipHash, $username)) {
            return $user;
        }
        
        $this->logFailure($ipHash, $username, $ipAddress);

        return null;
    }

    /**
     * @param string $ipHash
     * @param string $username
     * @param string $ipAddress
     *
     * @throws OrmException
     */
    protected function logFailure(string $ipHash, string $username, string $ipAddress)
    {
        $ipAddress = $this->loginLogIp ? $ipAddress : null;

        $loginAttempt = new LoginAttempt(0, $ipHash, $username, $ipAddress);
        $this->loginAttemptRepo->add($loginAttempt);
        $this->unitOfWork->commit();
    }

    /**
     * @param string $ipAddress
     *
     * @return string
     */
    public function getHash(string $ipAddress): string
    {
        return md5($ipAddress);
    }
}
