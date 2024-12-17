<?php

namespace App\Contracts;

use App\Models\EmailSupplier;
use App\Models\Report;
use App\Models\Supplier;

interface MarketContract
{
    public function unload(Supplier|EmailSupplier $supplier, Report $report): void;
}
