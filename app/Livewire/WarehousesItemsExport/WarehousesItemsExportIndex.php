<?php

namespace App\Livewire\WarehousesItemsExport;

use App\Livewire\BaseComponent;
use App\Models\User;
use App\Models\WarehousesItemsExportReport;
use Illuminate\Support\Facades\Storage;

class WarehousesItemsExportIndex extends BaseComponent
{
    public User $model;

    public function download($report)
    {

        $report = WarehousesItemsExportReport::find($report['id']);

        if ($report->status === 2) abort(403);

        return response()->download(
            file: Storage::disk('public')->path('users/warehouses/' . "{$report->uuid}.xlsx"),
            name: 'Склады ' . $this->model->name. "_{$report->updated_at}.xlsx"
        );
    }

    public function destroy($report)
    {
        $report = WarehousesItemsExportReport::find($report['id']);

        if ($report->status === 2) abort(403);

        Storage::disk('public')->delete('users/warehouses/' . "{$report->uuid}.xlsx");
        $report->delete();
    }

    public function render()
    {
        return view('livewire.warehouses-items-export.warehouses-items-export-index', [
            'warehousesItemsExportReports' => $this->model->warehousesItemsExportReports
        ]);
    }
}
