<?php

namespace App\Livewire\Warehouse;

use App\Exports\WarehousesStocksExport;
use App\Jobs\Warehouse\Export;
use App\Jobs\Warehouse\Import;
use App\Livewire\BaseComponent;
use App\Livewire\Forms\Warehouse\WarehousePostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSort;
use App\Models\Warehouse;
use App\Services\WarehouseItemsExportReportService;
use App\Services\WarehouseItemsImportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('Склады')]
class WarehouseIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public WarehousePostForm $form;

    public function destroy($id): void
    {
        $warehouse = Warehouse::findOrFail($id);

        $this->authorizeForUser($this->user(), 'delete', $warehouse);

        $warehouse->delete();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function warehouses()
    {
        return $this->currentUser()
            ->warehouses()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function store(): void
    {
        $this->authorizeForUser($this->user(), 'create', Warehouse::class);

        $this->form->store();

        \Flux::modal('create-warehouse')->close();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-warehouses')) {
            abort(403);
        }

        return view('livewire.warehouse.warehouse-index');
    }
}
