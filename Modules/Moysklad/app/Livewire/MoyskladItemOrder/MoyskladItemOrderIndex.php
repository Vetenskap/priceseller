<?php

namespace Modules\Moysklad\Livewire\MoyskladItemOrder;

use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSaveButton;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWebhook;
use Modules\Moysklad\Services\MoyskladWebhookService;

class MoyskladItemOrderIndex extends Component
{
    use WithJsNotifications, WithSaveButton;

    public Moysklad $moysklad;

    #[Validate]
    public $enabled_orders;

    #[Validate]
    public $clear_order_time;

    public function rules(): array
    {
        return [
            'enabled_orders' => ['nullable', 'boolean'],
            'clear_order_time' => ['nullable', 'integer']
        ];
    }

    public function mount(): void
    {
        $this->enabled_orders = (bool) $this->moysklad->enabled_orders;
        $this->clear_order_time = $this->moysklad->clear_order_time;
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
        $this->validate();

        $this->moysklad->enabled_orders = $this->enabled_orders;
        $this->moysklad->clear_order_time = $this->clear_order_time;
        $this->moysklad->save();
        $this->addSuccessSaveNotification();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-item-order.moysklad-item-order-index');
    }
}
