<?php

namespace App\Livewire\WarehousesItemsExport;

use App\Models\User;
use App\Models\WarehousesItemsExportReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class WarehousesItemsExportIndex extends Component
{
    public User $model;

    public function getListeners()
    {
        return [
            'echo:notification.' . $this->model->id . ',.notify' => '$refresh',
            'echo:items-import-report.' . $this->model->id . ',.event' => '$refresh'
        ];
    }

    public function download($report)
    {

        $report = WarehousesItemsExportReport::find($report['id']);

        return response()->download(
            file: Storage::disk('public')->path('users/warehouses/' . "{$report->uuid}.xlsx"),
            name: 'Склады ' . $this->model->name. "_{$report->updated_at}.xlsx"
        );
    }

    public function destroy($report)
    {
        $report = WarehousesItemsExportReport::find($report['id']);

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
