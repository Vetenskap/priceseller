<?php

namespace App\Livewire\ItemsImportReport;

use App\Exports\ItemsExport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
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
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ItemsImportReportIndex extends BaseComponent
{
    use WithModelsPaths, WithPagination, WithJsNotifications;

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
    public function itemsImportReports()
    {
        return $this->model
            ->itemsImportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function destroy($id): void
    {
        $report = ItemsImportReport::find($id);

        $this->authorize('delete', $report);

        ItemsImportReportService::destroy($report, $this->model);

        $this->addSuccessDeleteNotification();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.items-import-report.items-import-report-index');
    }
}
