<?php

namespace Modules\Moysklad\Livewire\MoyskladWarehouse;

use App\Livewire\Traits\WithJsNotifications;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladWarehouse\MoyskladWarehousePostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;
use Modules\Moysklad\Services\MoyskladWebhookService;
class MoyskladWarehouseIndex extends Component
{
    use WithJsNotifications;

    public MoyskladWarehousePostForm $form;

    public Moysklad $moysklad;

    public function store(): void
    {
        $this->form->store();
    }

    #[On('delete-warehouse')]
    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
    }

    public function update(): void
    {
        $this->dispatch('update-warehouse')->component(MoyskladWarehouseEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function addWebhook(): void
    {
        MoyskladWebhookService::addWarehouseWebhook($this->moysklad);
    }

    public function deleteWebhook(): void
    {
        MoyskladWebhookService::deleteWarehouseWebhook($this->moysklad);
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-warehouse.moysklad-warehouse-index', [
            'moyskladWarehouses' => (new MoyskladService($this->moysklad))->getAllWarehouses()
        ]);
    }
}
