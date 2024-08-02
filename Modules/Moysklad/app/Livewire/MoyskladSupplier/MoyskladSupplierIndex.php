<?php

namespace Modules\Moysklad\Livewire\MoyskladSupplier;

use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladSupplierIndex extends Component
{
    public Moysklad $moysklad;

    public Collection $moyskladSuppliers;

    public $supplierId;

    public function save()
    {
        $this->dispatch('save.moysklad.suppliers');
    }

    public function mount()
    {
        $service = new MoyskladService($this->moysklad);
        $this->moyskladSuppliers = $service->getAllSuppliers();
    }

    public function add()
    {
        $this->moysklad->suppliers()->updateOrCreate([
            'supplier_id' => $this->supplierId
        ], [
            'supplier_id' => $this->supplierId
        ]);
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-supplier.moysklad-supplier-index');
    }
}
