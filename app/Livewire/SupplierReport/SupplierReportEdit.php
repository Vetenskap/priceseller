<?php

namespace App\Livewire\SupplierReport;

use App\Exports\SupplierReportLogsExport;
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
        $this->authorize('delete', $this->report);

        $this->report->delete();

        return redirect()->route('supplier-edit', ['supplier' => $this->report->supplier_id]);
    }

    public function render()
    {
        $this->authorize('view', $this->report);

        return view('livewire.supplier-report.supplier-report-edit');
    }

    public function unloadAllLogs()
    {
        return \Excel::download(new SupplierReportLogsExport($this->report), 'report.xlsx');
    }
}
