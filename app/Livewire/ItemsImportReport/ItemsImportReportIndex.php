<?php

namespace App\Livewire\ItemsImportReport;

use App\Exports\ItemsExport;
use App\Livewire\Traits\WithModelsPaths;
use App\Models\ItemsImportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ItemsImportReportIndex extends Component
{
    use WithModelsPaths;

    public User|WbMarket|OzonMarket $model;

    public function getListeners()
    {
        return [
            'echo:notification.' . auth()->user()->id . ',.notify' => '$refresh',
            "echo:items-import-report.{$this->model->id},.event" => '$refresh',
        ];
    }

    public function deleteImport($report)
    {
        $report = ItemsImportReport::find($report['id']);

        $this->authorize('delete', $report);

        Storage::disk('public')->delete($this->getPath() . "{$report->uuid}.xlsx");
        $report->delete();
    }

    public function render()
    {
        return view('livewire.items-import-report.items-import-report-index', [
            'itemsImportReports' => $this->model->itemsImportReports
        ]);
    }
}
