<?php

namespace Modules\Moysklad\Livewire\MoyskladItemOrder;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladWebhookService;

class MoyskladItemOrderIndex extends Component
{
    use WithJsNotifications;

    public Moysklad $moysklad;
    public $enabled_orders;

    public function mount(): void
    {
        $this->enabled_orders = $this->moysklad->enabled_orders;
    }

    public function deleteWebhook(array $webhook): void
    {
        $webhook = MoyskladWebhook::find($webhook['id']);

        $service = new MoyskladWebhookService($this->moysklad, $webhook);
        $service->deleteWebhook();
    }

    public function addWebhook(): void
    {
        $data = collect(config('moysklad.available_webhooks'))->where('type', 'customerorder')->where('action', 'CREATE')->first();

        MoyskladWebhookService::createWebhook($this->moysklad, $data);
    }

    public function clear(): void
    {
        $this->moysklad->itemsOrders()->where('new', true)->update([
            'new' => false
        ]);
    }

    public function save(): void
    {
        $this->moysklad->enabled_orders = $this->enabled_orders;
        $this->moysklad->save();
        $this->addSuccessSaveNotification();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item-order.moysklad-item-order-index');
    }
}
