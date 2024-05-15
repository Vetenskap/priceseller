<?php

namespace App\Components\EmailClient;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Storage;

interface EmailClient
{
    public function getNewPrice(Collection $supplies, int $userId, $criteria = 'UNSEEN'): ?string;

}
