<?php

namespace App\Livewire\ItemsMoyskladImportReport;

use App\Models\ItemsMoyskladImportReport;
use App\Models\Moysklad;
use App\Services\MoyskladService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ItemsMoyskladImportReportIndex extends Component
{
    public Moysklad $moysklad;

    public function deleteImport($report)
    {
        $report = ItemsMoyskladImportReport::find($report['id']);

        Storage::disk('public')->delete(MoyskladService::PATH . "{$report->uuid}.xlsx");
        $report->delete();
    }

    public function render()
    {
        return view('livewire.items-moysklad-import-report.items-moysklad-import-report-index', [
            'itemsImportReports' => $this->moysklad->itemsImportReports
        ]);
    }
}
