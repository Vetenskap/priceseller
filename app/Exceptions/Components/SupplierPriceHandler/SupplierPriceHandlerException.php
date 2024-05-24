<?php

namespace App\Exceptions\Components\SupplierPriceHandler;

class SupplierPriceHandlerException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
