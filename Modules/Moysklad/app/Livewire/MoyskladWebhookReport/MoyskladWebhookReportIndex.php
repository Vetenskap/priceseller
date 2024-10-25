<?php

namespace Modules\Moysklad\Livewire\MoyskladWebhookReport;

use App\Livewire\Traits\WithSort;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Models\MoyskladWebhook;

class MoyskladWebhookReportIndex extends Component
{
    use WithPagination, WithSort;

    public MoyskladWebhook $webhook;

    #[Computed]
    public function reports()
    {
        return $this->webhook
            ->reports()
            ->paginate();
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-webhook-report.moysklad-webhook-report-index');
    }
}
