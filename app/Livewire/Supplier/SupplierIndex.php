<?php

namespace App\Livewire\Supplier;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Models\Supplier;

class SupplierIndex extends BaseComponent
{
    public SupplierPostForm $form;

    public $showCreateBlock = false;

    public function add()
    {
        $this->showCreateBlock = ! $this->showCreateBlock;
    }

    public function store()
    {
        if (! auth()->user()->permission_ms) {
            $this->form->ms_uuid = null;
        }

        $this->authorize('create', Supplier::class);

        $this->form->store();
    }

    public function changeOpen($supplier)
    {
        $supplier = Supplier::find($supplier['id']);

        $this->authorize('update', $supplier);

        $supplier->open = !$supplier->open;
        $supplier->save();
    }

    public function destroy($supplier)
    {
        $supplier = Supplier::find($supplier['id']);

        $this->authorize('delete', $supplier);

        $supplier->delete();
    }

    public function render()
    {
        return view('livewire.supplier.supplier-index', [
            'suppliers' => Supplier::where('user_id', auth()->user()->id)->get()
        ]);
    }
}
