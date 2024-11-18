<?php

namespace Modules\Moysklad\Livewire\MoyskladItemReport;

use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithSort;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Models\MoyskladItemApiReport;

class MoyskladItemReportShow extends Component
{
    use WithSort, WithPagination, WithFilters;

    public MoyskladItemApiReport $report;

    #[Computed]
    public function items()
    {
        return $this->tapQuery($this->report->items()->filters());
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-item-report.moysklad-item-report-show');
    }
}
