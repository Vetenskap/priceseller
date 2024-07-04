<?php

namespace App\Livewire\WarehousesItemsImport;

use App\Models\User;
use App\Models\WarehousesItemsImportReport;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class WarehousesItemsImportIndex extends Component
{
    public User $model;

    public function getListeners()
    {
        return [
            'echo:notification.' . auth()->user()->id . ',.notify' => 'render',
            "echo:items-import-report.{$this->model->id},.event" => 'render',
            "warehouses-items-import-created" => 'render',
        ];
    }

    public function deleteImport($report)
    {
        $report = WarehousesItemsImportReport::find($report['id']);

        Storage::disk('public')->delete('users/warehouses/' . "{$report->uuid}.xlsx");
        $report->delete();
    }

    public function render()
    {
        return view('livewire.warehouses-items-import.warehouses-items-import-index', [
            'warehousesItemsImportReports' => $this->model->warehousesItemsImportReports
        ]);
    }
}
