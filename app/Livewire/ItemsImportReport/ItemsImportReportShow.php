<?php

namespace App\Livewire\ItemsImportReport;

use App\Models\ItemsImportReport;
use Livewire\Component;

class ItemsImportReportShow extends Component
{
    public ItemsImportReport $report;

    public function render()
    {
        return view('livewire.items-import-report.items-import-report-show');
    }
}
