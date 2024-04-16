<?php

namespace App\Livewire\Supplier;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Models\Supplier;
use Livewire\Component;

class SupplierIndex extends Component
{
    public SupplierPostForm $form;

    public $suppliers;

    public $showCreateBlock = false;

    public function mount()
    {
        $this->suppliers = Supplier::where('user_id', auth()->user()->id)->get();
    }

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

        $supplier = $this->form->store();

        $this->suppliers->add($supplier);

        $this->js((new Toast("Поставщик: {$supplier->name}", 'Поставщик успешно создан'))->success());
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);

        $supplier->delete();
    }

    public function render()
    {
        return view('livewire.supplier.supplier-index');
    }
}
