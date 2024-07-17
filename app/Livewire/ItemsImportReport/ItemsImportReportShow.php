<?php

namespace App\Livewire\ItemsImportReport;

use App\Livewire\BaseComponent;
use App\Models\ItemsImportReport;
use Livewire\Component;

class ItemsImportReportShow extends BaseComponent
{
    public ItemsImportReport $report;

    public function render()
    {
        return view('livewire.items-import-report.items-import-report-show');
    }
}
