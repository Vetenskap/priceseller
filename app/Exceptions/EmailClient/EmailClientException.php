<?php

namespace App\Exceptions\EmailClient;

class EmailClientException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
