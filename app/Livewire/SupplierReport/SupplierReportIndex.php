<?php

namespace App\Livewire\SupplierReport;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;

class SupplierReportIndex extends BaseComponent
{
    use WithJsNotifications;

    public Supplier $supplier;

    public function render()
    {
        return view('livewire.supplier-report.supplier-report-index', [
            'reports' => $this->supplier->reports
        ]);
    }
}
