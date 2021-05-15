<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Config;

use AbterPhp\Admin\Constant\Env;
use AbterPhp\Framework\Config\Routes as FrameworkRoutes;
use AbterPhp\Framework\Environments\Environment;
use AbterPhp\Framework\Exception\Config as ConfigException;

class Routes extends FrameworkRoutes
{
    public const DASHBOARD_PATH = '/dashboard';
    public const PROFILE_PATH   = '/profile';

    protected ?string $loginPath = null;

    protected ?string $logoutPath = null;

    protected ?string $adminBasePath = null;

    protected ?string $apiBasePath = null;

    protected ?string $uploadUrl = null;

    /**
     * @param string $loginPath
     */
    public function setLoginPath(string $loginPath): void
    {
        $this->loginPath = $loginPath;
    }

    /**
     * @return string
     */
    public function getLoginPath(): string
    {
        if (null !== $this->loginPath) {
            return $this->loginPath;
        }

        $loginPath = Environment::getVar(Env::ADMIN_LOGIN_PATH);
        if (null === $loginPath) {
            throw new ConfigException(__CLASS__, [Env::ADMIN_LOGIN_PATH]);
        }

        $this->loginPath = $loginPath;

        return $this->loginPath;
    }

    /**
     * @param string $logoutPath
     */
    public function setLogoutPath(string $logoutPath): void
    {
        $this->logoutPath = $logoutPath;
    }

    /**
     * @return string
     */
    public function getLogoutPath(): string
    {
        if (null !== $this->logoutPath) {
            return $this->logoutPath;
        }

        $logoutPath = Environment::getVar(Env::ADMIN_LOGOUT_PATH);
        if (null === $logoutPath) {
            throw new ConfigException(__CLASS__, [Env::ADMIN_LOGOUT_PATH]);
        }

        $this->logoutPath = $logoutPath;

        return $this->logoutPath;
    }

    /**
     * @param string $adminBasePath
     */
    public function setAdminBasePath(string $adminBasePath): void
    {
        $this->adminBasePath = $adminBasePath;
    }

    /**
     * @return string
     */
    public function getAdminBasePath(): string
    {
        if (null !== $this->adminBasePath) {
            return $this->adminBasePath;
        }

        $adminBasePath = Environment::getVar(Env::ADMIN_BASE_PATH);
        if (null === $adminBasePath) {
            throw new ConfigException(__CLASS__, [Env::ADMIN_BASE_PATH]);
        }

        $this->adminBasePath = $adminBasePath;

        return $adminBasePath;
    }

    /**
     * @param string $apiBasePath
     */
    public function setApiBasePath(string $apiBasePath): void
    {
        $this->apiBasePath = $apiBasePath;
    }

    /**
     * @return string
     */
    public function getApiBasePath(): string
    {
        if (null !== $this->apiBasePath) {
            return $this->apiBasePath;
        }

        $apiBasePath = Environment::getVar(Env::API_BASE_PATH);
        if (null === $apiBasePath) {
            throw new ConfigException(__CLASS__, [Env::API_BASE_PATH]);
        }

        $this->apiBasePath = $apiBasePath;

        return $apiBasePath;
    }

    /**
     * @return string
     */
    public function getLoginFailurePath(): string
    {
        return $this->getLoginPath();
    }

    /**
     * @return string
     */
    public function getLoginSuccessPath(): string
    {
        return $this->getAdminBasePath() . static::DASHBOARD_PATH;
    }

    /**
     * @return string
     */
    public function getProfilePath(): string
    {
        return $this->getAdminBasePath() . static::PROFILE_PATH;
    }

    /**
     * @param string $uploadUrl
     */
    public function setUploadUrl(string $uploadUrl): void
    {
        $this->uploadUrl = $uploadUrl;
    }

    /**
     * @return string
     */
    public function getUploadUrl(): string
    {
        if (null !== $this->uploadUrl) {
            return $this->uploadUrl;
        }

        $uploadUrl = sprintf(
            '%s%s%s',
            rtrim($this->getMediaUrl(), DIRECTORY_SEPARATOR),
            DIRECTORY_SEPARATOR,
            ltrim(Environment::mustGetVar(Env::EDITOR_BASE_PATH), DIRECTORY_SEPARATOR)
        );

        $this->uploadUrl = $uploadUrl;

        return $uploadUrl;
    }
}
