<?php

namespace App\Contracts;

use App\Models\EmailSupplier;
use App\Models\Report;

interface SupplierEmailUnloadContract
{
    public function unload(): void;
    public function nullAllStocks(): void;
    public function nullUpdated(): void;
    public function make(EmailSupplier $supplier, string $path, Report $report): void;
}
