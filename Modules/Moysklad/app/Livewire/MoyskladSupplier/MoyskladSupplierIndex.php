<?php

namespace Modules\Moysklad\Livewire\MoyskladSupplier;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Livewire\Forms\MoyskladSupplier\MoyskladSupplierPostForm;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladSupplierIndex extends Component
{
    use WithJsNotifications, WithPagination;

    public MoyskladSupplierPostForm $form;
    public Moysklad $moysklad;

    public function mount(): void
    {
        $this->form->setMoysklad($this->moysklad);
    }

    #[Computed]
    public function suppliers(): LengthAwarePaginator
    {
        return $this->moysklad
            ->suppliers()
            ->paginate();
    }

    public function store(): void
    {
        $this->form->store();
    }

    public function destroy($id): void
    {
        $supplier = $this->moysklad->suppliers()->find($id);
        $supplier->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('moysklad::livewire.moysklad-supplier.moysklad-supplier-index', [
            'moyskladSuppliers' => (new MoyskladService($this->moysklad))->getAllSuppliers()
        ]);
    }
}
