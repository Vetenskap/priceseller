<?php

namespace App\Contracts;

interface EmailHandlerContract
{
    public function getNewPrice(string $supplierEmail, string $supplierFilename, string $address, string $password): ?string;
}
