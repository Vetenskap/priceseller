<?php

namespace App\Livewire\ItemsExportReport;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\ItemsExportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\ItemsExportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemsExportReportIndex extends BaseComponent
{
    use WithPagination;

    public User|WbMarket|OzonMarket|Warehouse $model;

    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function itemsExportReport()
    {
        return $this->model
            ->itemsExportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function download($id): BinaryFileResponse
    {
        $report = ItemsExportReport::find($id);

        return ItemsExportReportService::download($report, $this->model);
    }

    public function destroy($id): void
    {
        $report = ItemsExportReport::find($id);

        $this->authorize('delete', $report);

        ItemsExportReportService::destroy($report, $this->model);

        $this->addSuccessDeleteNotification();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.items-export-report.items-export-report-index');
    }
}
