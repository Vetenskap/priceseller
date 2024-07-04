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
            'echo:notification.' . auth()->user()->id . ',.notify' => 'render',
            "warehouses-items-export-created" => 'render',
        ];
    }

    public function download($report)
    {

        $report = WarehousesItemsExportReport::find($report['id']);

        return response()->download(
            file: Storage::disk('public')->path('users/warehouses/' . "{$report->uuid}.xlsx"),
            name: 'Склады ' . \auth()->user()->name . "_{$report->updated_at}.xlsx"
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
