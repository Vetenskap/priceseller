<?php

namespace App\Livewire\Supplier;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\Supplier\SupplierPostForm;
use App\Models\Supplier;
use Livewire\Component;

class SupplierEdit extends Component
{
    public SupplierPostForm $form;

    public Supplier $supplier;

    public function mount()
    {
        $this->form->setSupplier($this->supplier);
    }

    public function save()
    {
        $this->authorize('update', $this->supplier);

        $this->form->update();

        $this->js((new Toast("Поставщик: {$this->supplier->name}", 'Данные успешно обновлены'))->success());
    }

    public function destroy()
    {
        $this->authorize('delete', $this->supplier);
    }

    public function render()
    {
        return view('livewire.supplier.supplier-edit');
    }
}
