<?php

namespace App\Livewire\Warehouse;

use App\Exports\WarehousesStocksExport;
use App\Jobs\Warehouse\Export;
use App\Jobs\Warehouse\Import;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Warehouse;
use App\Services\WarehouseItemsExportReportService;
use App\Services\WarehouseItemsImportReportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class WarehouseIndex extends Component
{
    use WithSubscribeNotification, WithFileUploads, WithJsNotifications;

    public WarehousePostForm $form;

    public $showCreateForm = false;

    public $file;

    #[Url]
    public $page = null;

    public function add()
    {
        $this->showCreateForm = !$this->showCreateForm;
    }

    public function create()
    {
        $this->form->store();

        $this->reset('showCreateForm');
    }

    public function downloadTemplate()
    {
        return \Excel::download(new WarehousesStocksExport(auth()->user(), true), 'Склады_шаблон.xlsx');
    }

    public function destroy($warehouse)
    {
        $warehouse = Warehouse::find($warehouse['id']);

        $this->authorize('delete', $warehouse);

        $warehouse->delete();
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-index', [
            'warehouses' => auth()->user()->warehouses
        ]);
    }

    public function export()
    {
        if (WarehouseItemsExportReportService::get(auth()->user())) {
            $this->addJobAlready();
            return;
        }

        Export::dispatch(auth()->user());
        $this->addJobNotification();
    }

    public function import()
    {
        if (WarehouseItemsImportReportService::get(auth()->user())) {
            $this->addJobAlready();
            return;
        }

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs('/users/warehouses/', $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        Import::dispatch(auth()->user(), $uuid);
        $this->addJobNotification();
    }
}
