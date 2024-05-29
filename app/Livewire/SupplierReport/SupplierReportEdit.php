<?php

namespace App\Livewire\SupplierReport;

use App\Livewire\Traits\WithJsNotifications;
use App\Models\SupplierReport;
use Livewire\Component;

class SupplierReportEdit extends Component
{
    use WithJsNotifications;

    public SupplierReport $report;

    public function getListeners()
    {
        return [
            "echo:supplier.report.{$this->report->supplier->id},.change-message" => 'render'
        ];
    }

    public function destroy()
    {
        $this->report->delete();

        return redirect()->route('supplier-edit', ['supplier' => $this->report->supplier_id]);
    }

    public function render()
    {
        return view('livewire.supplier-report.supplier-report-edit');
    }
}
