<?php

namespace Modules\BergApi\Contracts;

use App\Models\Report;
use Modules\BergApi\Models\BergApi;

interface BergUnloadContract
{
    public function make(BergApi $bergApi, Report $report): void;
    public function getNewPrice(): void;
    public function nullUpdated(): void;
    public function nullAllStocks(): void;
}
