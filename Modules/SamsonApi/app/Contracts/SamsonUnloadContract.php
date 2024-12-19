<?php

namespace Modules\SamsonApi\Contracts;

use App\Models\Report;
use Modules\SamsonApi\Models\SamsonApi;

interface SamsonUnloadContract
{
    public function make(SamsonApi $samsonApi, Report $report): void;
    public function getNewPrice(): void;
    public function nullUpdated(): void;
    public function nullAllStocks(): void;
}
