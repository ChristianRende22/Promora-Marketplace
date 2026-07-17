<?php

namespace App\Exceptions;

use Exception;

class RuleValidationException extends Exception
{
    private string $errorCode;

    public function __construct(string $errorCode, string $message = "")
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
