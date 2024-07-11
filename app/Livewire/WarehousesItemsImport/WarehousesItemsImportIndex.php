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
            'echo:notification.' . $this->model->id . ',.notify' => '$refresh',
            'echo:items-import-report.' . $this->model->id . ',.event' => '$refresh'
        ];
    }

    public function deleteImport($report)
    {
        $report = WarehousesItemsImportReport::find($report['id']);

        if ($report->status === 2) abort(403);

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
