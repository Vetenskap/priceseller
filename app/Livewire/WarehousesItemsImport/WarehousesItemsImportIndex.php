<?php

namespace App\Livewire\WarehousesItemsImport;

use App\Exports\WarehousesStocksExport;
use App\Jobs\Warehouse\Import;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\User;
use App\Models\WarehousesItemsImportReport;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WarehousesItemsImportIndex extends BaseComponent
{
    use WithFileUploads, WithSort, WithPagination;

    public $file;

    public User $model;

    public function downloadTemplate(): BinaryFileResponse
    {
        return \Excel::download(new WarehousesStocksExport(auth()->user(), true), 'Склады_шаблон.xlsx');
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

    public function destroy($report): void
    {
        $report = WarehousesItemsImportReport::find($report['id']);

        if ($report->status === 2) abort(403);

        Storage::disk('public')->delete('users/warehouses/' . "{$report->uuid}.xlsx");
        $report->delete();
    }

    #[Computed]
    public function warehousesItemsImportReports()
    {
        return auth()
            ->user()
            ->warehousesItemsImportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.warehouses-items-import.warehouses-items-import-index');
    }
}
