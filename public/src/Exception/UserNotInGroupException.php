<?php

namespace App\Exception;

class UserNotInGroupException extends \Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}
