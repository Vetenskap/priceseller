<?php

namespace App\Livewire\WbWarehouse;

use App\Livewire\BaseComponent;
use App\Models\WbWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class WbWarehouseEdit extends BaseComponent
{
    public WbWarehouse $warehouse;

    public $name;

    public $warehouse_id;

    public function update(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->warehouse->market);

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
        $this->authorizeForUser($this->user(), 'delete', $this->warehouse->market);

        $this->warehouse->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-warehouse.wb-warehouse-edit');
    }
}
