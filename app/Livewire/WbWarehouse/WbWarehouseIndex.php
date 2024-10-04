<?php

namespace App\Livewire\WbWarehouse;

use App\Livewire\BaseComponent;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class WbWarehouseIndex extends BaseComponent
{
    public WbMarket $market;
    public $apiWarehouses;

    public $selectedWarehouse = null;

    public function store(): void
    {
        if (!$this->selectedWarehouse) {
            \Flux::toast('Не выбран склад', 'Ошибка');
            return;
        }

        $this->authorize('create', WbWarehouse::class);

        $name = collect($this->apiWarehouses)->firstWhere('id', $this->selectedWarehouse)['name'];

        $this->market->warehouses()->updateOrCreate([
            'warehouse_id' => $this->selectedWarehouse,
        ], [
            'warehouse_id' => $this->selectedWarehouse,
            'name' => $name
        ]);
    }


    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-warehouse.wb-warehouse-index');
    }
}
