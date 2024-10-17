<?php

namespace App\Livewire\BundlesExportReport;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\BundlesExportReport;
use App\Services\Bundle\BundlesExportReportService;
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
        $status = $this->checkTtlJob(\App\Jobs\Bundle\BundlesExport::getUniqueId($this->currentUser()), \App\Jobs\Bundle\BundlesExport::class);

        if ($status) \App\Jobs\Bundle\BundlesExport::dispatch($this->currentUser());
    }

    #[Computed]
    public function bundlesExportReports()
    {
        return $this->currentUser()
            ->bundlesExportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function destroy($id): void
    {
        $report = BundlesExportReport::find($id);

        BundlesExportReportService::destroy($report);
    }

    public function download($id): BinaryFileResponse
    {
        $report = BundlesExportReport::find($id);

        return BundlesExportReportService::download($report);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.bundles-export-report.bundles-export-report-index');
    }
}
