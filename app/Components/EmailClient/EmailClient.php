<?php

namespace App\Components\EmailClient;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;

interface EmailClient
{
    public function getNewPrice(string $supplierEmail, string $supplierFilename, int $userId, $criteria = 'UNSEEN'): ?string;

}
