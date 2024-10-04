<?php

namespace App\Livewire\WarehousesItemsExport;

use App\Jobs\Warehouse\Export;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\User;
use App\Models\WarehousesItemsExportReport;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WarehousesItemsExportIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public User $model;

    public function download($id): BinaryFileResponse
    {

        $report = WarehousesItemsExportReport::find($id);

        if ($report->status === 2) abort(403);

        return response()->download(
            file: Storage::disk('public')->path('users/warehouses/' . "{$report->uuid}.xlsx"),
            name: 'Склады ' . $this->model->name . "_{$report->updated_at}.xlsx"
        );
    }

    public function export(): void
    {
        $status = $this->checkTtlJob(Export::getUniqueId(auth()->user()), Export::class);
        if ($status) Export::dispatch(auth()->user());
    }

    public function destroy($id): void
    {
        $report = WarehousesItemsExportReport::find($id);

        if ($report->status === 2) abort(403);

        Storage::disk('public')->delete('users/warehouses/' . "{$report->uuid}.xlsx");
        $report->delete();
    }

    #[Computed]
    public function warehousesItemsExportReports()
    {
        return auth()
            ->user()
            ->warehousesItemsExportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.warehouses-items-export.warehouses-items-export-index');
    }
}
