<?php

namespace App\Livewire\Warehouse;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Warehouse;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class WarehouseEdit extends BaseComponent
{
    use WithFileUploads, WithJsNotifications, WithFilters;

    public WarehousePostForm $form;

    public Warehouse $warehouse;

    #[Session('WarehouseEdit.{warehouse.id}')]
    public $selectedTab = 'main';

    /** @var TemporaryUploadedFile $file */
    public $file;

//    public function import()
//    {
//        if (!$this->file) $this->dispatch('livewire-upload-error');
//
//        $uuid = Str::uuid();
//        $ext = $this->file->getClientOriginalExtension();
//
//        $path = $this->file->storeAs(WarehouseService::PATH, $uuid . '.' . $ext);
//
//        if (!Storage::exists($path)) {
//            $this->dispatch('livewire-upload-error');
//            return;
//        }
//
//        Import::dispatch($uuid, $ext, $this->warehouse, WarehouseService::class);
//
//        $this->dispatch('items-import-report-created');
//    }
//
//    public function export()
//    {
//        Export::dispatch($this->warehouse, WarehouseService::class);
//
//        $this->dispatch('items-export-report-created');
//    }

    public function mount()
    {
        $this->form->setWarehouse($this->warehouse);
    }

    public function save()
    {
        $this->authorize('update', $this->warehouse);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        $this->authorize('view', $this->warehouse);

        return view('livewire.warehouse.warehouse-edit');
    }
}
