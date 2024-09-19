<?php

namespace App\Livewire\EmailSupplierStockValue;

use App\Livewire\Forms\EmailSupplierStockValuePostForm;
use App\Models\EmailSupplier;
use App\Models\EmailSupplierStockValue;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailSupplierStockValueEdit extends Component
{
    public EmailSupplier $emailSupplier;

    public EmailSupplierStockValue $stockValue;

    public EmailSupplierStockValuePostForm $form;

    public function mount(): void
    {
        $this->form->setEmailSupplier($this->emailSupplier);
        $this->form->setStockValue($this->stockValue);
    }

    #[On('email-supplier-stock-value-updated')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('email-supplier-stock-value-deleted')->component(EmailSupplierStockValueIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.email-supplier-stock-value.email-supplier-stock-value-edit');
    }
}
