<?php

namespace App\Exceptions\Components\EmailClient;

class EmailClientException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
