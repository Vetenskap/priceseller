<?php

namespace Modules\BergApi\Livewire\Forms\BergApiWarehouse;

use Livewire\Form;
use Modules\BergApi\Models\BergApiWarehouse;

class BergApiWarehousePostForm extends Form
{
    public ?BergApiWarehouse $bergApiWarehouse;
    public string $name;
    public int $warehouse_name;

    public function setBergApiWarehouse(?BergApiWarehouse $bergApiWarehouse)
    {
        $this->bergApiWarehouse = $bergApiWarehouse;
        if ($bergApiWarehouse) {
            $this->name = $bergApiWarehouse->name;
            $this->warehouse_name = $bergApiWarehouse->warehouse_name;
        }
    }

    public function create()
    {
        auth()->user()->bergApi->warehouses()->updateOrCreate([
            'warehouse_name' => $this->warehouse_name
        ], $this->only([
            'warehouse_name',
            'name'
        ]));
    }

    public function update()
    {
        $this->bergApiWarehouse->update($this->only([
            'warehouse_name',
            'name'
        ]));
    }
}
