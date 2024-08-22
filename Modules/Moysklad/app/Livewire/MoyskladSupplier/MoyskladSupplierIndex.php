<?php

namespace Modules\Moysklad\Livewire\MoyskladSupplier;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Livewire\Forms\MoyskladSupplier\MoyskladSupplierPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladSupplierIndex extends Component
{
    use WithJsNotifications;

    public MoyskladSupplierPostForm $form;
    public Moysklad $moysklad;

    #[On('delete-supplier')]
    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function update(): void
    {
        $this->dispatch('update-supplier')->component(MoyskladSupplierEdit::class);
        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-supplier.moysklad-supplier-index', [
            'moyskladSuppliers' => (new MoyskladService($this->moysklad))->getAllSuppliers()
        ]);
    }
}
