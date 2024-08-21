<?php

namespace Modules\BergApi\Livewire\BergApiWarehouse;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\BergApi\Models\BergApiWarehouse;

class BergApiWarehouseEdit extends Component
{
    public BergApiWarehouse $warehouse;

    public $name;
    public $warehouse_id;

    public function mount(): void
    {
        $this->name = $this->warehouse->name;
        $this->warehouse_id = $this->warehouse->warehouse_id;
    }

    public function destroy(): void
    {
        $this->warehouse->delete();
        $this->dispatch('delete-warehouse')->component(BergApiWarehouseIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-warehouse.berg-api-warehouse-edit');
    }
}
