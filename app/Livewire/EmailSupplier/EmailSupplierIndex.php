<?php

namespace App\Livewire\EmailSupplier;

use App\Models\Email;
use App\Models\EmailSupplier;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailSupplierIndex extends Component
{
    public Email $email;

    public function render()
    {
        return view('livewire.email-supplier.email-supplier-index');
    }

    public function store($id)
    {

        $this->authorize('create', EmailSupplier::class);

        $this->email->suppliers()->attach($id, [
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
