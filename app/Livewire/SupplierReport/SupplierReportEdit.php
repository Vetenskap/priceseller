<?php

namespace App\Livewire\SupplierReport;

use App\Exports\SupplierReportLogsExport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSort;
use App\Models\SupplierReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use LaravelIdea\Helper\App\Models\_IH_SupplierReportLog_C;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SupplierReportEdit extends BaseComponent
{
    use WithSort, WithPagination;

    public SupplierReport $report;

    #[Computed]
    public function logs(): LengthAwarePaginator|array|\Illuminate\Pagination\LengthAwarePaginator|_IH_SupplierReportLog_C
    {
        return $this->report
            ->logs()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function destroy(): RedirectResponse
    {
        $this->authorize('delete', $this->report);

        $this->report->delete();

        return redirect()->back();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorize('view', $this->report);

        return view('livewire.supplier-report.supplier-report-edit');
    }

    public function unloadAllLogs(): BinaryFileResponse
    {
        return \Excel::download(new SupplierReportLogsExport($this->report), 'report.xlsx');
    }
}
