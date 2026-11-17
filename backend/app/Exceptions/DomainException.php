<?php

namespace App\Exceptions;

use Exception;

/** A business rule said no. Rendered as HTTP 422 with the message (bootstrap/app.php). */
class DomainException extends Exception
{
    public function __construct(string $message, public string $rule = '')
    {
        parent::__construct($message);
    }
}
