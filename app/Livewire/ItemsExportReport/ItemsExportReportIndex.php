<?php

namespace App\Livewire\ItemsExportReport;

use App\Livewire\Traits\WithModelsPaths;
use App\Models\ItemsExportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ItemsExportReportIndex extends Component
{
    use WithModelsPaths;

    public User|WbMarket|OzonMarket $model;

    public function getListeners()
    {
        return [
            'echo:notification.' . auth()->user()->id . ',.notify' => 'render',
            "items-export-report-created" => 'render',
        ];
    }

    public function download($report)
    {

        $report = ItemsExportReport::find($report['id']);

        return response()->download(
            file: Storage::disk('public')->path($this->getPath() . "{$report->uuid}.xlsx"),
            name: $this->getFilename() . "_{$report->updated_at}.xlsx"
        );
    }

    public function destroy($report)
    {
        $report = ItemsExportReport::find($report['id']);

        $this->authorize('delete', $report);

        Storage::disk('public')->delete($this->getPath() . "{$report->uuid}.xlsx");
        $report->delete();
    }

    public function render()
    {
        return view('livewire.items-export-report.items-export-report-index', [
            'itemsExportReport' => $this->model->itemsExportReports
        ]);
    }
}
