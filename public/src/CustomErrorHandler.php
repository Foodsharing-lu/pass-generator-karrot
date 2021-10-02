<?php

namespace App;

use Slim\Handlers\ErrorHandler;

class CustomErrorHandler extends ErrorHandler
{
    protected function logError(string $error): void
    {
        $uri = $this->request->getUri();
        $this->logger->error($error, ['requestedUri' => (string) $uri]);
    }
}
