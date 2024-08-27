<?php

namespace Modules\Moysklad\Livewire\MoyskladChangeWarehouse;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladWebhookService;

class MoyskladChangeWarehouseIndex extends Component
{
    public Moysklad $moysklad;

    public function deleteWebhook(array $webhook): void
    {
        $webhook = MoyskladWebhook::find($webhook['id']);

        $service = new MoyskladWebhookService($this->moysklad, $webhook);
        $service->deleteWebhook();
    }

    public function addWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'demand')->where('action', 'CREATE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data, true);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-change-warehouse.moysklad-change-warehouse-index');
    }
}
