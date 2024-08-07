<?php

namespace App\Livewire\EmailSupplier;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Models\Email;
use App\Models\EmailSupplier;

class EmailSupplierIndex extends BaseComponent
{
    public Email $email;

    public $selectedSupplier;

    public function render()
    {
        return view('livewire.email-supplier.email-supplier-index');
    }

    public function store()
    {
        if (!$this->selectedSupplier) {
            $this->js((new Toast('Ошибка', "Не выбран поставщик"))->danger());
            return;
        }

        $this->authorize('create', EmailSupplier::class);

        if ($this->email->suppliers()->wherePivot('supplier_id', $this->selectedSupplier)->exists()) {
            $this->js((new Toast('Ошибка', "Указаный поставщик уже добавлен"))->danger());
            return;
        }

        $this->email->suppliers()->attach($this->selectedSupplier, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function delete($supplier)
    {
        $this->authorize('delete', EmailSupplier::where('supplier_id', $supplier['id'])->where('email_id', $this->email->id)->first());

        $this->email->suppliers()->detach($supplier['id']);
    }
}
