<?php

namespace App\Livewire\SupplierReport;

use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use Livewire\Component;

class SupplierReportIndex extends Component
{
    use WithJsNotifications;

    public Supplier $supplier;

    public function getListeners()
    {
        return [
            "echo:supplier.report.{$this->supplier->id},.change-message" => 'render'
        ];
    }

    public function render()
    {
        return view('livewire.supplier-report.supplier-report-index', [
            'reports' => $this->supplier->reports
        ]);
    }
}
