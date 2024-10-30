<?php

namespace App\Livewire\BundlesExportReport;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\BundlesExportReport;
use App\Services\Bundle\BundlesExportReportService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BundlesExportReportIndex extends BaseComponent
{
    use WithSort, WithPagination;

    public function export(): void
    {
        if (!$this->user()->can('view-bundles')) {
            abort(403);
        }

        $status = $this->checkTtlJob(\App\Jobs\Bundle\BundlesExport::getUniqueId($this->currentUser()), \App\Jobs\Bundle\BundlesExport::class);

        if ($status) \App\Jobs\Bundle\BundlesExport::dispatch($this->currentUser());
    }

    #[Computed]
    public function bundlesExportReports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->bundlesExportReports());
    }

    public function destroy($id): void
    {
        if (!$this->user()->can('view-bundles')) {
            abort(403);
        }

        $report = BundlesExportReport::find($id);

        BundlesExportReportService::destroy($report);
    }

    public function download($id): BinaryFileResponse
    {
        if (!$this->user()->can('view-bundles')) {
            abort(403);
        }

        $report = BundlesExportReport::find($id);

        return BundlesExportReportService::download($report);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-bundles')) {
            abort(403);
        }

        return view('livewire.bundles-export-report.bundles-export-report-index');
    }
}
