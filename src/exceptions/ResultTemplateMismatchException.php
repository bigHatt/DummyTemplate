<?php

namespace App\Exceptions;

class ResultTemplateMismatchException extends \Exception
{
    public function __construct($message = 'Result not matches original template')
    {
        parent::__construct($message);
    }
}