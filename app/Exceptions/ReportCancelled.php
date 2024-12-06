<?php

namespace App\Exceptions;

class ReportCancelled extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
