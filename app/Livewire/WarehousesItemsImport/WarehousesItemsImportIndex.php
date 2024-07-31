<?php

namespace App\Livewire\WarehousesItemsImport;

use App\Livewire\BaseComponent;
use App\Models\User;
use App\Models\WarehousesItemsImportReport;
use Illuminate\Support\Facades\Storage;

class WarehousesItemsImportIndex extends BaseComponent
{
    public User $model;


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
