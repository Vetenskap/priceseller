<?php

namespace Modules\VoshodApi\Livewire\VoshodApiWarehouse;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\VoshodApi\Models\VoshodApiWarehouse;

class VoshodApiWarehouseEdit extends Component
{
    public VoshodApiWarehouse $warehouse;

    public $label;

    public function mount(): void
    {
        $this->label = collect(config('voshodapi.warehouses'))->where('name', $this->warehouse->name)->first()['label'];
    }

    public function destroy(): void
    {
        $this->warehouse->delete();
        $this->dispatch('delete-warehouse')->component(VoshodApiWarehouseIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-warehouse.voshod-api-warehouse-edit');
    }
}
