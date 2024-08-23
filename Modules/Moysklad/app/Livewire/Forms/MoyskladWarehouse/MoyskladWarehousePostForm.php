<?php

namespace Modules\Moysklad\Livewire\Forms\MoyskladWarehouse;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;

class MoyskladWarehousePostForm extends Form
{
    public Moysklad $moysklad;
    public ?MoyskladWarehouseWarehouse $moyskladWarehouse = null;

    #[Validate]
    public $moysklad_warehouse_uuid;
    #[Validate]
    public $warehouse_id;

    public function setMoysklad(Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function setMoyskladWarehouse(MoyskladWarehouseWarehouse $moyskladWarehouse): void
    {
        $this->moyskladWarehouse = $moyskladWarehouse;
        $this->moysklad_warehouse_uuid = $moyskladWarehouse->moysklad_warehouse_uuid;
        $this->warehouse_id = $moyskladWarehouse->warehouse_id;
    }

    public function rules(): array
    {
        return [
            'moysklad_warehouse_uuid' => ['required', 'uuid'],
            'warehouse_id' => ['required', 'uuid'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->moysklad->warehouses()->create($this->except(['moysklad', 'moyskladWarehouse']));

        $this->reset(['moysklad_warehouse_uuid', 'warehouse_id']);
    }

    public function update(): void
    {
        $this->validate();

        $this->moyskladWarehouse->update($this->except(['moysklad', 'moyskladWarehouse']));
    }

    public function destroy(): void
    {
        $this->moyskladWarehouse->delete();
    }

}
