<?php

namespace Modules\Moysklad\Livewire\MoyskladWebhookReport;

use App\Livewire\Traits\WithSort;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Jobs\MoyskladWebhookProcess;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Models\MoyskladWebhookReport;

class MoyskladWebhookReportIndex extends Component
{
    use WithPagination, WithSort;

    public MoyskladWebhook $webhook;

    #[Computed]
    public function reports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->webhook->reports());
    }

    public function repeatAll(): void
    {
        foreach ($this->webhook->reports->filter(fn (MoyskladWebhookReport $report) => $report->events()->where('status', true)->count() !== $report->events->count() && $report->events->count()) as $report) {
            MoyskladWebhookProcess::dispatch($report->payload, $report->moyskladWebhook);
            $report->delete();
        }

        \Flux::toast('Все необратанные вебхуки отправлены на повторную обработку');
    }

    public function repeat($id): void
    {
        $report = MoyskladWebhookReport::find($id);

        if ($report->events()->where('status', true)->count() === $report->events->count() && $report->events->count()) {
            abort(403);
        }

        if ($report->payload->isNotEmpty()) {
            MoyskladWebhookProcess::dispatch($report->payload, $report->moyskladWebhook);
            $report->delete();
        } else {
            \Flux::toast('Нет данных', variant: 'danger');
        }
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-webhook-report.moysklad-webhook-report-index');
    }
}
