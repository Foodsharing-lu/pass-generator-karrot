<?php

namespace App;

use Monolog\Logger;

class Interactor
{
    private const CONFIG_FILE_PATH = __DIR__ . '/../config/config.php';

    private Config $config;
    private Authenticator $authenticator;

    public function __construct($clientHandler = null)
    {
        $this->config = Config::load(self::CONFIG_FILE_PATH);
        $serverUrl = $this->config->get('karrot-url') . '/' . 'api/';
        $this->authenticator = new Authenticator($serverUrl, $clientHandler);
    }

    public function logIn(string $eMailAddress, string $password): void
    {
        $groupId = $this->config->get('karrot-group-id');
        $this->authenticator->logIn($eMailAddress, $password, $groupId);
    }

    public function isLoggedIn(): bool
    {
        return $this->authenticator->isLoggedIn();
    }

    public function getUserId(): string
    {
        return $this->authenticator->getUserId();
    }

    public function getUserName(): string
    {
        return $this->authenticator->getUserName();
    }

    public function getUserPhotoUrl(): string
    {
        return $this->authenticator->getUserPhotoUrl();
    }

    public function logOut()
    {
        $this->authenticator->logOut();
    }

    public function getAbsolutePathToPassFolder(): string
    {
        return __DIR__ . '/../' . $this->config->get('pass-folder-path');
    }

    public function getRelativePathToPassFolder(): string
    {
        return $this->config->get('pass-folder-path');
    }

    public function getDisplayErrorDetails(): bool
    {
        return (bool)$this->config->get('display-error-details');
    }

    public function getPassUrlPrefix(): string
    {
        return $this->config->get('pass-url-prefix');
    }

    public function getSiteName(): string
    {
        return $this->config->get('site-name');
    }

    public function getLogger(): Logger
    {
        return LoggerWrapper::getLogger();
    }

    public function getAbsolutePathToPassImage(): string
    {
        $userId = $this->getUserId();
        $passImagePath = $this->getAbsolutePathToPassFolder();
        return PassManager::getCompletePassImagePath($passImagePath, $userId);
    }

    public function getRelativePathToPassImage(): string
    {
        $userId = $this->getUserId();
        $passImagePath = $this->getRelativePathToPassFolder();
        return PassManager::getCompletePassImagePath($passImagePath, $userId);
    }

    public function hasPass(): bool
    {
        $userId = $this->getUserId();
        $passImagePath = $this->getAbsolutePathToPassFolder();
        return PassManager::hasPass($passImagePath, $userId);
    }

    public function deletePass(): void
    {
        $userId = $this->getUserId();
        $passImagePath = $this->getAbsolutePathToPassFolder();
        PassManager::deletePass($passImagePath, $userId);
    }

    public function createPass(): void
    {
        if (!$this->hasUserFullName()) {
            throw new \Exception('User has no full name!');
        }
        if (!$this->hasUserPhoto()) {
            throw new \Exception('User has no photo!');
        }

        $id = $this->getUserId();
        $imageFolderPath = $this->getAbsolutePathToPassFolder();
        $name = $this->getUserName();
        $photoUrl = $this->getUserPhotoUrl();

        $fileName = PassManager::getFileName($id);
        $url = $this->getPassUrlPrefix() . $fileName;
        $qrCodeImageBlob = QRCodeImageGenerator::create($url, 150);

        PassManager::createPass($id, $imageFolderPath, $name, $photoUrl, $qrCodeImageBlob, $url);
    }

    public function hasUserFullName(): bool
    {
        $name = $this->getUserName();
        return NameChecker::hasMoreThanOneName($name);
    }

    public function hasUserPhoto(): bool
    {
        return !empty($this->getUserPhotoUrl());
    }
}
