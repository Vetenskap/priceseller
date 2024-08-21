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
        $badItems = $this->report->badItems()->paginate(10);

        return view('livewire.items-import-report.items-import-report-show', [
            'badItems' => $badItems
        ]);
    }
}
