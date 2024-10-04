<?php

namespace App\Livewire\BundlesImportReport;

use App\Exports\BundlesExport;
use App\Jobs\Bundle\BundlesImport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\BundlesImportReport;
use App\Services\Bundle\BundleService;
use App\Services\Bundle\BundlesImportReportService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BundlesImportReportIndex extends BaseComponent
{
    use WithSort, WithPagination, WithFileUploads;

    public $file;

    public function downloadTemplate(): BinaryFileResponse
    {
        return \Excel::download(new BundlesExport(auth()->user(), true), "priceseller_bundles_шаблон.xlsx");
    }

    public function import(): void
    {
        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(BundleService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(BundlesImport::getUniqueId(auth()->user()), BundlesImport::class);

        if ($status) BundlesImport::dispatch($uuid, $ext, auth()->user());
    }

    #[Computed]
    public function bundlesImportReports()
    {
        return auth()
            ->user()
            ->bundlesImportReports()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function destroy($id): void
    {
        $report = BundlesImportReport::find($id);

        BundlesImportReportService::destroy($report);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.bundles-import-report.bundles-import-report-index');
    }
}
