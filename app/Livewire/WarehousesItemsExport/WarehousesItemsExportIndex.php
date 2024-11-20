<?php

namespace App\Livewire\WarehousesItemsExport;

use App\Jobs\Warehouse\Export;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\User;
use App\Models\WarehousesItemsExportReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public function getListeners(): array
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'render',
        ];
    }

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
        $status = $this->checkTtlJob(Export::getUniqueId($this->currentUser()), Export::class);
        if ($status) Export::dispatch($this->currentUser());
    }

    public function destroy($id): void
    {
        $report = WarehousesItemsExportReport::find($id);

        if ($report->status === 2) abort(403);

        Storage::disk('public')->delete('users/warehouses/' . "{$report->uuid}.xlsx");
        $report->delete();
    }

    #[Computed]
    public function warehousesItemsExportReports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->warehousesItemsExportReports());
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.warehouses-items-export.warehouses-items-export-index');
    }
}
