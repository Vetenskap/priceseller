<?php

namespace App\Livewire\BundleItemsExportReport;

use App\Jobs\Bundle\BundleItemsExport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\BundleItemsExportReport;
use App\Services\Bundle\BundleItemsExportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BundleItemsExportReportIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public function export(): void
    {
        $status = $this->checkTtlJob(BundleItemsExport::getUniqueId(auth()->user()), BundleItemsExport::class);

        if ($status) BundleItemsExport::dispatch(auth()->user());
    }

    public function download($id): BinaryFileResponse
    {
        $report = BundleItemsExportReport::find($id);

        return BundleItemsExportReportService::download($report);
    }

    public function destroy($id): void
    {
        $report = BundleItemsExportReport::find($id);

        BundleItemsExportReportService::destroy($report);
    }

    #[Computed]
    public function bundleItemsExportReports()
    {
        return auth()
            ->user()
            ->bundleItemsExportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.bundle-items-export-report.bundle-items-export-report-index');
    }
}