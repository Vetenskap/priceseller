<?php

namespace Modules\Moysklad\Livewire\MoyskladSupplier;

use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Models\MoyskladSupplierSupplier;

class MoyskladSupplierEdit extends Component
{
    public MoyskladSupplierSupplier $supplier;

    public Collection $moyskladSuppliers;

    public $moyskladSupplierId;

    public function mount()
    {
        $this->moyskladSupplierId = $this->supplier->moysklad_supplier_uuid;
    }

    #[On('save.moysklad.suppliers')]
    public function save()
    {
        $name = collect($this->moyskladSuppliers->where('id', $this->moyskladSupplierId)->first())->get('name');
        $this->supplier->moysklad_supplier_name = $name;
        $this->supplier->moysklad_supplier_uuid = $this->moyskladSupplierId;
        $this->supplier->save();
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-supplier.moysklad-supplier-edit');
    }
}
