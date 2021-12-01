<?php

namespace App\Exceptions;

class InvalidTemplateException extends \Exception
{
    public function __construct($message = 'Invalid template')
    {
        parent::__construct($message);
    }
}