<?php

namespace Modules\Moysklad\Livewire\MoyskladWebhookReport;

use Illuminate\Support\Collection;
use Livewire\Component;

class MoyskladWebhookReportIndex extends Component
{
    public Collection $webhookReports;

    public function render()
    {
        return view('moysklad::livewire.moysklad-webhook-report.moysklad-webhook-report-index');
    }
}
