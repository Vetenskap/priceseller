<?php

namespace App\Livewire\Warehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Warehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class WarehouseEdit extends BaseComponent
{
    use WithJsNotifications;

    public $backRoute = 'warehouses.index';

    public WarehousePostForm $form;

    public Warehouse $warehouse;

    public function mount(): void
    {
        $this->form->setWarehouse($this->warehouse);
    }

    public function update(): void
    {
        $this->authorize('update', $this->warehouse);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->warehouse);

        return view('livewire.warehouse.warehouse-edit');
    }
}
