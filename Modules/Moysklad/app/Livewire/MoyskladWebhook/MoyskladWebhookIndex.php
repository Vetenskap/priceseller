<?php

namespace Modules\Moysklad\Livewire\MoyskladWebhook;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladWebhookService;

class MoyskladWebhookIndex extends Component
{
    public Moysklad $moysklad;

    #[Url]
    public $webhookId = null;

    public ?MoyskladWebhook $webhook;

    public function mount(): void
    {
        $this->webhook = MoyskladWebhook::find($this->webhookId);
    }

    public function disable(): void
    {
        $service = new MoyskladWebhookService($this->moysklad, $this->webhook);
        if ($this->webhook->type === 'warehouses') {
            MoyskladWebhookService::disableWarehouseWebhook($this->moysklad);
        } else {
            $service->disableWebhook();
        }
    }

    public function enable(): void
    {
        $service = new MoyskladWebhookService($this->moysklad, $this->webhook);
        if ($this->webhook->type === 'warehouses') {
            MoyskladWebhookService::enableWarehouseWebhook($this->moysklad);
        } else {
            $service->enableWebhook();
        }
    }

    public function delete(): void
    {
        $service = new MoyskladWebhookService($this->moysklad, $this->webhook);
        if ($this->webhook->type === 'warehouses') {
            MoyskladWebhookService::deleteWarehouseWebhook($this->moysklad);
        } else {
            $service->deleteWebhook();
        }
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-webhook.moysklad-webhook-index');
    }
}
