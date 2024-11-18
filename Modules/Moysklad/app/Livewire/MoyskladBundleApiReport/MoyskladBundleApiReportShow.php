<?php

namespace Modules\Moysklad\Livewire\MoyskladBundleApiReport;

use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithSort;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Models\MoyskladBundleApiReport;

class MoyskladBundleApiReportShow extends Component
{
    use WithSort, WithPagination, WithFilters;

    public MoyskladBundleApiReport $report;

    #[Computed]
    public function items()
    {
        return $this->tapQuery($this->report->items()->filters());
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-bundle-api-report.moysklad-bundle-api-report-show');
    }
}
