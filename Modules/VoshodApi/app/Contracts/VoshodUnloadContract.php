<?php

namespace Modules\VoshodApi\Contracts;

use App\Models\Report;
use Modules\VoshodApi\Models\VoshodApi;

interface VoshodUnloadContract
{
    public function make(VoshodApi $voshodApi, Report $report): void;
    public function getNewPrice(): void;
    public function nullUpdated(): void;

    public function nullAllStocks(): void;
}
