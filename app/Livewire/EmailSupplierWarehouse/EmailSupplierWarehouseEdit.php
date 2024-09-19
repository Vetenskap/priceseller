<?php

namespace App\Livewire\EmailSupplierWarehouse;

use App\Livewire\Forms\EmailSupplierWarehouse\EmailSupplierWarehousePostForm;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailSupplierWarehouseEdit extends Component
{
    public EmailSupplierWarehousePostForm $form;

    public EmailSupplierWarehouse $emailSupplierWarehouse;

    public EmailSupplier $emailSupplier;

    public function mount(): void
    {
        $this->form->setEmailSupplierWarehouse($this->emailSupplierWarehouse);
    }

    #[On('email-supplier-warehouse-update')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('email-supplier-warehouse-delete')->component(EmailSupplierWarehouseIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email-supplier-warehouse.email-supplier-warehouse-edit');
    }
}
