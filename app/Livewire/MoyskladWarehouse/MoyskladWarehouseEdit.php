<?php

namespace App\Livewire\MoyskladWarehouse;

use App\Models\MoyskladWarehouse;
use Livewire\Component;

class MoyskladWarehouseEdit extends Component
{
    public MoyskladWarehouse $warehouse;

    public $name;

    public $open;

    public function mount()
    {
        $this->name = $this->warehouse->name;
        $this->open = $this->warehouse->open;
    }

    public function destroy()
    {
        $this->warehouse->delete();
    }

    public function render()
    {
        return view('livewire.moysklad-warehouse.moysklad-warehouse-edit');
    }
}
