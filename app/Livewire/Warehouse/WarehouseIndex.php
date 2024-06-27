<?php

namespace App\Livewire\Warehouse;

use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Warehouse;
use Livewire\Component;

class WarehouseIndex extends Component
{
    use WithSubscribeNotification;

    public WarehousePostForm $form;

    public $showCreateForm = false;

    public function add()
    {
        $this->showCreateForm = ! $this->showCreateForm;
    }

    public function create()
    {
        $this->form->store();

        $this->reset('showCreateForm');
    }

    public function destroy($warehouse)
    {
        $warehouse = Warehouse::find($warehouse['id']);

        $warehouse->delete();
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-index', [
            'warehouses' => auth()->user()->warehouses
        ]);
    }
}
