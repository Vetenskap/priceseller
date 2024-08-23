<?php

namespace App\Livewire\ItemsImportReport;

use App\Exports\ItemsExport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithModelsPaths;
use App\Models\ItemsImportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ItemsImportReportIndex extends BaseComponent
{
    use WithModelsPaths;

    public User|WbMarket|OzonMarket|Warehouse $model;

    public function deleteImport($report): void
    {
        $report = ItemsImportReport::find($report['id']);

        $this->authorize('delete', $report);

        ItemsImportReportService::destroy($report, $this->model);

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.items-import-report.items-import-report-index', [
            'itemsImportReports' => $this->model->itemsImportReports
        ]);
    }
}
