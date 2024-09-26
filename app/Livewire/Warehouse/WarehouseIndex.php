<?php

namespace App\Livewire\Warehouse;

use App\Exports\WarehousesStocksExport;
use App\Jobs\Warehouse\Export;
use App\Jobs\Warehouse\Import;
use App\Livewire\BaseComponent;
use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Warehouse;
use App\Services\WarehouseItemsExportReportService;
use App\Services\WarehouseItemsImportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('Склады')]
class WarehouseIndex extends BaseComponent
{
    use WithFileUploads, WithJsNotifications;

    public WarehousePostForm $form;

    public $file = null;

    public $page;

    public function mount($page = 'list'): void
    {
        $this->page = $page;
    }

    public function store(): void
    {
        $this->authorize('create', Warehouse::class);

        $this->form->store();

    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return \Excel::download(new WarehousesStocksExport(auth()->user(), true), 'Склады_шаблон.xlsx');
    }

    public function export(): void
    {
        $status = $this->checkTtlJob(Export::getUniqueId(auth()->user()), Export::class);
        if ($status) Export::dispatch(auth()->user());
    }

    public function import(): void
    {
        if (!$this->file) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs('/users/warehouses/', $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(Import::getUniqueId(auth()->user()), Import::class);

        if ($status) Import::dispatch(auth()->user(), $uuid);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->page === 'list') {
            return view('livewire.warehouse.pages.warehouse-index-list-page', [
                'warehouses' => auth()->user()->warehouses
            ]);
        } else if ($this->page === 'stocks') {
            return view('livewire.warehouse.pages.warehouse-index-stocks-page');
        }

        abort(404);

    }
}
