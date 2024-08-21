<?php

namespace Modules\Moysklad\Livewire\MoyskladWarehouse;

use App\Livewire\Components\Toast;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Services\MoyskladService;
use Modules\Moysklad\Services\MoyskladWarehouseWarehouseService;
use MoyskladWarehouse\MoyskladWarehousePostForm;

class MoyskladWarehouseEdit extends Component
{

    public MoyskladWarehousePostForm $form;

    public MoyskladWarehouseWarehouse $warehouse;

    public Moysklad $moysklad;

    public function mount(): void
    {
        $this->form->setMoyskladWarehouse($this->warehouse);
        $this->moysklad = $this->warehouse->moysklad;
    }

    #[On('update-warehouse')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-warehouse')->component(MoyskladWarehouseIndex::class);
    }

    public function updateStocks(): void
    {
        $service = new MoyskladWarehouseWarehouseService($this->warehouse, $this->moysklad);
        $count = $service->updateAllStocks();

        $this->js((new Toast('Успех', 'Количество обновленных остатков: ' . $count))->success());
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-warehouse.moysklad-warehouse-edit', [
            'moyskladWarehouses' => (new MoyskladService($this->moysklad))->getAllWarehouses()
        ]);
    }
}
