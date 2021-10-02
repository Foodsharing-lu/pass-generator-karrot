<?php

namespace App\Exception;

class MissingConfigOptionException extends \Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
