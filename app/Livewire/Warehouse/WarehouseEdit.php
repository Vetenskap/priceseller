<?php

namespace App\Livewire\Warehouse;

use App\Jobs\Export;
use App\Jobs\Import;
use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class WarehouseEdit extends Component
{
    use WithFileUploads, WithJsNotifications, WithFilters, WithSubscribeNotification;

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
        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-edit');
    }
}
