<?php

namespace App\Exceptions\Components\SupplierPriceHandler;

class SupplierPriceHandlerException extends \RuntimeException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
