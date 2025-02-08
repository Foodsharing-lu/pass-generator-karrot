<?php

namespace App;

use App\Exception\ConnectException;
use App\Exception\InvalidCredentialsException;
use App\Exception\SessionException;
use App\Exception\UserNotInGroupException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

class Authenticator
{
    private Client $client;

    public function __construct(string $serverUrl, $clientHandler = null)
    {
        $parameters = [];
        $parameters['base_uri'] = $serverUrl;
        if ($clientHandler != null) {
            // used for testing
            $parameters['handler'] = $clientHandler;
        }
        $this->client = new Client($parameters);
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['token']) && $_SESSION['token'] !== '';
    }

    public function logIn(string $eMailAddress, string $password, array $groupIds): void
    {
        if (!isset($eMailAddress) || $eMailAddress === '' || !isset($password) || $password === '') {
            throw new InvalidCredentialsException();
        }
        try {
            $tokenResponse = $this->client->request('POST', 'auth/token/', [
                'json' => [
                    'username' => $eMailAddress,
                    'password' => $password,
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException) {
            throw new ConnectException();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $eRequest = Psr7\Message::toString($e->getRequest());
            $eResponse = Psr7\Message::toString($e->getResponse());
            throw new InvalidCredentialsException($eRequest . ' ' . $eResponse);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $eRequest = Psr7\Message::toString($e->getRequest());
            $eResponse = '';
            if ($e->hasResponse()) {
                $eResponse = Psr7\Message::toString($e->getResponse());
            }
            throw new \Exception($eRequest . ' ' . $eResponse);
        }
        $tokenResponseBody = json_decode($tokenResponse->getBody(), true);
        $token = $tokenResponseBody['token'];
        try {
            $userResponse = $this->client->request('GET', 'auth/user/', [
                'headers' => [
                    'Authorization' => 'Token ' . $token
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ConnectException) {
            throw new ConnectException();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $eRequest = Psr7\Message::toString($e->getRequest());
            $eResponse = '';
            if ($e->hasResponse()) {
                $eResponse = Psr7\Message::toString($e->getResponse());
            }
            throw new \Exception($eRequest . ' ' . $eResponse);
        }
        $userResponseBody = json_decode($userResponse->getBody(), true);
        $userId = $userResponseBody['id'];
        $userName = $userResponseBody['display_name'];
        $userPhotoUrl = '';
        if (!empty($userResponseBody['photo_urls'])) {
            $userPhotoUrl = $userResponseBody['photo_urls']['full_size'];
        }
        $isMemberOfOneGroup = false;
        for ($i = 0; $i < count($groupIds); $i++) {
            $groupId = $groupIds[$i];
            try {
                $groupResponse = $this->client->request('GET', 'groups/' . $groupId, [
                    'headers' => [
                        'Authorization' => 'Token ' . $token
                    ]
                ]);
            } catch (\GuzzleHttp\Exception\ConnectException) {
                throw new ConnectException();
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // A 404 error response is returned when the user requests the details of a group they are not member of.
                continue;
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $eRequest = Psr7\Message::toString($e->getRequest());
                $eResponse = '';
                if ($e->hasResponse()) {
                    $eResponse = Psr7\Message::toString($e->getResponse());
                }
                throw new \Exception($eRequest . ' ' . $eResponse);
            }
            $groupResponseBody = json_decode($groupResponse->getBody(), true);
            if (!in_array($userId, $groupResponseBody['members'])) {
                continue;
            } else {
                $isMemberOfOneGroup = true;
                break;
            }
        }
        if (!$isMemberOfOneGroup) {
            throw new UserNotInGroupException('User with id ' . $userId . ' not in any of the groups: ' . implode(", ", $groupIds));
        }
        if ($tokenResponse->getStatusCode() == 200) {
            LoggerWrapper::info('User logged in', ['id' => $userId]);
            $_SESSION['token'] = $token;
            $_SESSION['id'] = $userId;
            $_SESSION['name'] = $userName;
            $_SESSION['photoUrl'] = $userPhotoUrl;
        }
    }

    public function getUserId(): string
    {
        if (!isset($_SESSION['id'])) {
            throw new SessionException('"id" is not set in session.');
        }
        return $_SESSION['id'];
    }

    public function getUserName(): string
    {
        if (!isset($_SESSION['name'])) {
            throw new SessionException('"name" is not set in session.');
        }
        return $_SESSION['name'];
    }

    public function getUserPhotoUrl(): string
    {
        if (!isset($_SESSION['photoUrl'])) {
            throw new SessionException('"photoUrl" is not set in session.');
        }
        return $_SESSION['photoUrl'];
    }

    public function logOut(): void
    {
        $response = $this->client->request('POST', 'auth/logout/');
        if ($response->getStatusCode() == 200) {
            $userId = '';
            try {
                $userId = $this->getUserId();
            } catch (SessionException) {
            }
            LoggerWrapper::info('User logged out', ['id' => $userId]);
            session_unset();
        }
    }
}
