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
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        $status = $this->checkTtlJob(BundleItemsExport::getUniqueId($this->currentUser()), BundleItemsExport::class);

        if ($status) BundleItemsExport::dispatch($this->currentUser());
    }

    public function download($id): BinaryFileResponse
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        $report = BundleItemsExportReport::find($id);

        return BundleItemsExportReportService::download($report);
    }

    public function destroy($id): void
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        $report = BundleItemsExportReport::find($id);

        BundleItemsExportReportService::destroy($report);
    }

    #[Computed]
    public function bundleItemsExportReports()
    {
        return $this->currentUser()
            ->bundleItemsExportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        return view('livewire.bundle-items-export-report.bundle-items-export-report-index');
    }
}
