<?php

namespace App\Exception;

class ConnectException extends \Exception
{
    public function __construct($message = '', \Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
