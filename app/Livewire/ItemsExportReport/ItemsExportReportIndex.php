<?php

namespace App\Livewire\ItemsExportReport;

use App\Livewire\BaseComponent;
use App\Models\ItemsExportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\ItemsExportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemsExportReportIndex extends BaseComponent
{

    public User|WbMarket|OzonMarket|Warehouse $model;

    public function download($report): BinaryFileResponse
    {
        $report = ItemsExportReport::find($report['id']);

        return ItemsExportReportService::download($report, $this->model);
    }

    public function destroy($report): void
    {
        $report = ItemsExportReport::find($report['id']);

        $this->authorize('delete', $report);

        ItemsExportReportService::destroy($report, $this->model);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.items-export-report.items-export-report-index', [
            'itemsExportReport' => $this->model->itemsExportReports
        ]);
    }
}
