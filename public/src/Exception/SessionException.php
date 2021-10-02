<?php

namespace App\Exception;

class SessionException extends \Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
