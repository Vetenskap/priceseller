<?php

namespace Modules\Moysklad\Livewire\MoyskladWebhookReport;

use App\Livewire\Traits\WithSort;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Models\MoyskladWebhookReport;

class MoyskladWebhookReportShow extends Component
{
    use WithSort, WithPagination;

    public MoyskladWebhookReport $report;

    #[Computed]
    public function events(): LengthAwarePaginator
    {
        return $this->tapQuery($this->report->events());
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-webhook-report.moysklad-webhook-report-show');
    }
}
