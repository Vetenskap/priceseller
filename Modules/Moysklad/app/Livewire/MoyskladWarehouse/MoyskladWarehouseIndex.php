<?php

namespace Modules\Moysklad\Livewire\MoyskladWarehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LaravelIdea\Helper\Modules\Moysklad\Models\_IH_MoyskladWarehouseWarehouse_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Modules\Moysklad\Livewire\Forms\MoyskladWarehouse\MoyskladWarehousePostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Services\MoyskladService;
use Modules\Moysklad\Services\MoyskladWarehouseWarehouseService;
use Modules\Moysklad\Services\MoyskladWebhookService;
class MoyskladWarehouseIndex extends BaseComponent
{
    use WithPagination;

    public MoyskladWarehousePostForm $form;

    public Moysklad $moysklad;

    #[Computed]
    public function warehouses(): LengthAwarePaginator|array|\Illuminate\Pagination\LengthAwarePaginator|_IH_MoyskladWarehouseWarehouse_C
    {
        return $this->moysklad
            ->warehouses()
            ->paginate();
    }

    public function destroy($id): void
    {
        $warehouse = MoyskladWarehouseWarehouse::find($id);

        // TODO: add authorize action
//        $this->authorize('delete', $warehouse);
        $warehouse->delete();
    }

    public function store(): void
    {
        $this->form->store();

        \Flux::modal('create-moysklad-warehouse')->close();
    }

    public function updateStocks($id): void
    {
        $warehouse = MoyskladWarehouseWarehouse::find($id);

        $service = new MoyskladWarehouseWarehouseService($warehouse, $this->moysklad);
        $count = $service->updateAllStocks();

        \Flux::toast('Количество обновленных остатков: ' . $count, 'Успех');
    }

    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
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
