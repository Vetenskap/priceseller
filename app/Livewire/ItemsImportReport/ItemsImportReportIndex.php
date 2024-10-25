<?php

namespace App\Livewire\ItemsImportReport;

use App\Exports\ItemsExport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithModelsPaths;
use App\Livewire\Traits\WithSort;
use App\Models\ItemsImportReport;
use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ItemsImportReportIndex extends BaseComponent
{
    use WithModelsPaths, WithPagination, WithJsNotifications, WithSort;

    public User|WbMarket|OzonMarket|Warehouse $model;

    #[Computed]
    public function itemsImportReports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->model->itemsImportReports());

    }

    public function destroy($id): void
    {
        $report = ItemsImportReport::find($id);

        $this->authorizeForUser($this->user(), 'delete', $report);

        ItemsImportReportService::destroy($report, $this->model);

        $this->addSuccessDeleteNotification();

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.items-import-report.items-import-report-index');
    }
}
