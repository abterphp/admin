<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Config;

use AbterPhp\Framework\Exception\Config as ConfigException;
use Opulence\Environments\Environment;

class Routes
{
    public const DASHBOARD_PATH = '/dashboard';
    public const PROFILE_PATH   = '/profile';

    private const ADMIN_LOGIN_PATH  = 'ADMIN_LOGIN_PATH';
    private const ADMIN_LOGOUT_PATH = 'ADMIN_LOGOUT_PATH';
    private const ADMIN_BASE_PATH   = 'ADMIN_BASE_PATH';
    private const API_BASE_PATH     = 'API_BASE_PATH';

    /** @var string|null */
    protected static $loginPath;

    /** @var string|null */
    protected static $logoutPath;

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

        $loginPath = Environment::getVar(static::ADMIN_LOGIN_PATH);
        if (null === $loginPath) {
            throw new ConfigException(__CLASS__, [static::ADMIN_LOGIN_PATH]);
        }

        static::$loginPath = (string)$loginPath;

        return static::$loginPath;
    }

    /**
     * @param string $logoutPath
     */
    public static function setLogoutPath(string $logoutPath): void
    {
        static::$logoutPath = $logoutPath;
    }

    /**
     * @return string
     */
    public static function getLogoutPath(): string
    {
        if (null !== static::$logoutPath) {
            return static::$logoutPath;
        }

        $logoutPath = Environment::getVar(static::ADMIN_LOGOUT_PATH);
        if (null === $logoutPath) {
            throw new ConfigException(__CLASS__, [static::ADMIN_LOGOUT_PATH]);
        }

        static::$logoutPath = (string)$logoutPath;

        return static::$logoutPath;
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

        $adminBasePath = Environment::getVar(static::ADMIN_BASE_PATH);
        if (null === $adminBasePath) {
            throw new ConfigException(__CLASS__, [static::ADMIN_BASE_PATH]);
        }

        static::$adminBasePath = (string)$adminBasePath;

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

        $apiBasePath = Environment::getVar(static::API_BASE_PATH);
        if (null === $apiBasePath) {
            throw new ConfigException(__CLASS__, [static::API_BASE_PATH]);
        }

        static::$apiBasePath = (string)$apiBasePath;

        return static::$apiBasePath;
    }

    /**
     * @return string
     */
    public static function getLoginFailurePath(): string
    {
        return static::getLoginPath();
    }

    /**
     * @return string
     */
    public static function getLoginSuccessPath(): string
    {
        return static::getAdminBasePath() . static::DASHBOARD_PATH;
    }

    /**
     * @return string
     */
    public static function getProfilePath(): string
    {
        return static::getAdminBasePath() . static::PROFILE_PATH;
    }
}
