<?php

namespace App\Livewire\SupplierReport;

use App\Exports\SupplierReportLogsExport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\SupplierReport;

class SupplierReportEdit extends BaseComponent
{
    use WithJsNotifications;

    public SupplierReport $report;

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
