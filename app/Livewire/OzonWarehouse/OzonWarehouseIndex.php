<?php

namespace App\Livewire\OzonWarehouse;

use App\Livewire\BaseComponent;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OzonWarehouseIndex extends BaseComponent
{
    public OzonMarket $market;
    public $apiWarehouses;

    public $selectedWarehouse = null;

    public function store(): void
    {
        if (!$this->selectedWarehouse) {
            \Flux::toast('Не выбран склад', 'Ошибка');
            return;
        }

        $this->authorizeForUser($this->user(), 'update', $this->market);

        $name = collect($this->apiWarehouses)->firstWhere('warehouse_id', $this->selectedWarehouse)['name'];

        $this->market->warehouses()->updateOrCreate([
            'warehouse_id' => $this->selectedWarehouse,
        ], [
            'warehouse_id' => $this->selectedWarehouse,
            'name' => $name
        ]);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-warehouse.ozon-warehouse-index');
    }
}
