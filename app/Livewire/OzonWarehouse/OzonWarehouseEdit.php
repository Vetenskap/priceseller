<?php

namespace App\Livewire\OzonWarehouse;

use App\Livewire\BaseComponent;
use App\Models\OzonWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OzonWarehouseEdit extends BaseComponent
{
    public OzonWarehouse $warehouse;

    public $name;

    public $warehouse_id;

    public function update(): void
    {
        $this->authorize('update', $this->warehouse);

        $this->warehouse->update($this->only('name'));

        $this->addSuccessSaveNotification();
    }

    public function mount(): void
    {
        $this->name = $this->warehouse->name;
        $this->warehouse_id = $this->warehouse->warehouse_id;
    }

    public function destroy(): void
    {
        $this->authorize('delete', $this->warehouse);

        $this->warehouse->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->warehouse);

        return view('livewire.ozon-warehouse.ozon-warehouse-edit');
    }
}
