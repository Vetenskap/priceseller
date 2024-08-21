<?php

namespace Modules\Moysklad\Livewire\MoyskladSupplier;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladSupplierSupplier;
use Modules\Moysklad\Services\MoyskladService;
use MoyskladSupplier\MoyskladSupplierPostForm;

class MoyskladSupplierEdit extends Component
{
    public MoyskladSupplierPostForm $form;
    public MoyskladSupplierSupplier $moyskladSupplier;

    public Moysklad $moysklad;

    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
        $this->form->setMoyskladSupplier($this->moyskladSupplier);
    }

    #[On('update-supplier')]
    public function update(): void
    {
        $this->form->update();
    }

    public function destroy(): void
    {
        $this->form->destroy();
        $this->dispatch('delete-supplier')->component(MoyskladSupplierIndex::class);
    }


    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-supplier.moysklad-supplier-edit', [
            'moyskladSuppliers' => (new MoyskladService($this->moysklad))->getAllSuppliers()
        ]);
    }
}
