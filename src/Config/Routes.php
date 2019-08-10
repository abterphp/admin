<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Config;

use AbterPhp\Framework\Exception\Config;
use Opulence\Environments\Environment;

class Routes
{
    const OPTION_NAME       = 'name';
    const OPTION_VARS       = 'vars';
    const OPTION_MIDDLEWARE = 'middleware';

    const ADMIN_LOGIN_PATH = 'ADMIN_LOGIN_PATH';
    const ADMIN_BASE_PATH  = 'ADMIN_BASE_PATH';
    const API_BASE_PATH    = 'API_BASE_PATH';

    /** @var string|null */
    protected static $loginPath;

    /** @var string|null */
    protected static $adminBasePath;

    /** @var string|null */
    protected static $apiBasePath;

    /**
     * @param string $loginPath
     */
    public static function setLoginPath(string $loginPath): void
    {
        static::$loginPath = $loginPath;
    }

    /**
     * @return string
     */
    public static function getLoginPath(): string
    {
        if (null !== static::$loginPath) {
            return static::$loginPath;
        }

        static::$loginPath = (string)Environment::getVar(static::ADMIN_LOGIN_PATH);
        if (null === static::$loginPath) {
            throw new Config(__CLASS__, [static::ADMIN_LOGIN_PATH]);
        }

        return static::$loginPath;
    }

    /**
     * @param string $adminBasePath
     */
    public static function setAdminBasePath(string $adminBasePath): void
    {
        static::$adminBasePath = $adminBasePath;
    }

    /**
     * @return string
     */
    public static function getAdminBasePath(): string
    {
        if (null !== static::$adminBasePath) {
            return static::$adminBasePath;
        }

        static::$adminBasePath = Environment::getVar(static::ADMIN_BASE_PATH);
        if (null === static::$adminBasePath) {
            throw new Config(__CLASS__, [static::ADMIN_BASE_PATH]);
        }

        return static::$adminBasePath;
    }

    /**
     * @param string $apiBasePath
     */
    public static function setApiBasePath(string $apiBasePath): void
    {
        static::$apiBasePath = $apiBasePath;
    }

    /**
     * @return string
     */
    public static function getApiBasePath(): string
    {
        if (null !== static::$apiBasePath) {
            return static::$apiBasePath;
        }

        static::$apiBasePath = Environment::getVar(static::API_BASE_PATH);
        if (null === static::$apiBasePath) {
            throw new Config(__CLASS__, [static::API_BASE_PATH]);
        }

        return static::$apiBasePath;
    }

    /**
     * @return string
     */
    public static function getLoginFailurePath(): string
    {
        return static::getAdminBasePath();
    }

    /**
     * @return string
     */
    public static function getLoginSuccessPath(): string
    {
        return static::getAdminBasePath() . \AbterPhp\Admin\Constant\Routes::PATH_DASHBOARD;
    }
}
