<?php

namespace BergApiWarehouse;

use Livewire\Form;
use Modules\BergApi\Models\BergApiWarehouse;

class BergApiWarehousePostForm extends Form
{
    public ?BergApiWarehouse $bergApiWarehouse;
    public string $name;
    public int $warehouse_id;

    public function setBergApiWarehouse(?BergApiWarehouse $bergApiWarehouse)
    {
        $this->bergApiWarehouse = $bergApiWarehouse;
        if ($bergApiWarehouse) {
            $this->name = $bergApiWarehouse->name;
            $this->warehouse_id = $bergApiWarehouse->warehouse_id;
        }
    }

    public function create()
    {
        auth()->user()->bergApi->warehouses()->updateOrCreate([
            'warehouse_id' => $this->warehouse_id
        ], $this->only([
            'warehouse_id',
            'name'
        ]));
    }

    public function update()
    {
        $this->bergApiWarehouse->update($this->only([
            'warehouse_id',
            'name'
        ]));
    }
}
