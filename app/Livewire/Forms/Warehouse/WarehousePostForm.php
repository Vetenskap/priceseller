<?php

namespace App\Livewire\Forms\Warehouse;

use App\Models\Warehouse;
use Illuminate\Support\Arr;
use Livewire\Form;

class WarehousePostForm extends Form
{
    public ?Warehouse $warehouse;

    public $name;

    public function setWarehouse(?Warehouse $warehouse = null)
    {
        $this->warehouse = $warehouse;
        $this->name = $this->warehouse->name;
    }

    public function store()
    {
        $warehouse = Warehouse::create(Arr::add($this->except('warehouse'), 'user_id', auth()->user()->id));

        $warehouse->refresh();

        $this->reset();

        return $warehouse;
    }

    public function update()
    {
        $this->warehouse->update($this->except('warehouse'));
    }
}
