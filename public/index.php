<?php

use App\CustomErrorHandler;
use App\Interactor;
use App\LoggerWrapper;
use App\Exception\ConnectException;
use App\Exception\InvalidCredentialsException;
use App\Exception\UserNotInGroupException;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

session_start();

require '../vendor/autoload.php';

$app = AppFactory::create();

$twig = Twig::create('assets/templates', ['cache' => false]);

$app->add(TwigMiddleware::create($app, $twig));

$interactor = new Interactor();
$displayErrorDetails = $interactor->getDisplayErrorDetails();
$logger = $interactor->getLogger();

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true, $logger);
$customErrorHandler = new CustomErrorHandler(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $logger,
);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

$app->get('/', function ($request, $response) use ($interactor) {
    $view = Twig::fromRequest($request);
    if ($interactor->isLoggedIn()) {
        $imagePath = '';
        if ($interactor->hasPass()) {
            $imagePath = $interactor->getRelativePathToPassImage();
        }
        $hasUserFullName = $interactor->hasUserFullName();
        $hasUserPhoto = $interactor->hasUserPhoto();
        $siteName = $interactor->getSiteName();
        return $view->render($response, 'overview.html', [
            'hasUserFullName' => $hasUserFullName,
            'imagePath' => $imagePath,
            'hasUserPhoto' => $hasUserPhoto,
            'siteName' => $siteName,
        ]);
    } else {
        $parameters = $request->getQueryParams();
        $variables = [];
        if (isset($parameters['error'])) {
            $variables['error'] = $parameters['error'];
        }
        if (isset($parameters['info'])) {
            $variables['info'] = $parameters['info'];
        }
        $variables['siteName'] = $interactor->getSiteName();
        return $view->render($response, 'login.html', $variables);
    }
})->setName('home');

$app->post('/', function ($request, $response) use ($app, $interactor) {
    $params = (array)$request->getParsedBody();
    $eMailAddress = $params['email'];
    $password = $params['password'];
    try {
        $interactor->logIn($eMailAddress, $password);
    } catch (ConnectException) {
        LoggerWrapper::warning('The server could not be connected to.');
        $routeParser = $app->getRouteCollector()->getRouteParser();
        $url = $routeParser->urlFor('home', [], ['error' => 'no-connection']);
        return $response->withHeader('Location', $url);
    } catch (InvalidCredentialsException $e) {
        LoggerWrapper::warning('The e-mail address or the password are wrong: ' . $e->getMessage());
        $routeParser = $app->getRouteCollector()->getRouteParser();
        $url = $routeParser->urlFor('home', [], ['error' => 'invalid-credentials']);
        return $response->withHeader('Location', $url);
    } catch (UserNotInGroupException $e) {
        LoggerWrapper::warning('The user is not in the group: ' . $e->getMessage());
        $routeParser = $app->getRouteCollector()->getRouteParser();
        $url = $routeParser->urlFor('home', [], ['error' => 'not-in-group']);
        return $response->withHeader('Location', $url);
    }
    if ($interactor->isLoggedIn()) {
        return $response->withHeader('Location', '/');
    } else {
        $routeParser = $app->getRouteCollector()->getRouteParser();
        $url = $routeParser->urlFor('home', [], ['error' => 'login-failed']);
        return $response->withHeader('Location', $url);
    }
});

$app->delete('/pass', function ($request, $response) use ($app, $interactor) {
    if (!$interactor->isLoggedIn()) {
        return $response->withStatus(403);
    }
    $interactor->deletePass();
    return $response->withStatus(200);
});

$app->post('/pass', function ($request, $response) use ($app, $interactor) {
    if (!$interactor->isLoggedIn()) {
        return $response->withStatus(403);
    }
    $interactor->createPass();
    return $response->withStatus(200);
});

$app->get('/logout', function ($request, $response) use ($app, $interactor) {
    $routeParser = $app->getRouteCollector()->getRouteParser();
    if (!$interactor->isLoggedIn()) {
        $url = $routeParser->urlFor('home', [], ['info' => 'not-logged-in']);
        return $response->withHeader('Location', $url);
    }
    $interactor->logOut();
    $url = $routeParser->urlFor('home', [], ['info' => 'logged-out']);
    return $response->withHeader('Location', $url);
});

$app->run();
