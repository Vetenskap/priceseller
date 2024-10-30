<?php

namespace App\Livewire\BundlesImportReport;

use App\Exports\BundlesExport;
use App\Jobs\Bundle\BundlesImport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\BundlesImportReport;
use App\Services\Bundle\BundleService;
use App\Services\Bundle\BundlesImportReportService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        if (!$this->user()->can('create-bundles') || !$this->user()->can('update-bundles') || !$this->user()->can('delete-bundles')) {
            abort(403);
        }

        return \Excel::download(new BundlesExport($this->currentUser(), true), "priceseller_bundles_шаблон.xlsx");
    }

    public function import(): void
    {
        if (!$this->user()->can('create-bundles') || !$this->user()->can('update-bundles') || !$this->user()->can('delete-bundles')) {
            abort(403);
        }

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(BundleService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(BundlesImport::getUniqueId($this->currentUser()), BundlesImport::class);

        if ($status) BundlesImport::dispatch($uuid, $ext, $this->currentUser());
    }

    #[Computed]
    public function bundlesImportReports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->bundlesImportReports());
    }

    public function destroy($id): void
    {
        if (!$this->user()->can('create-bundles') || !$this->user()->can('update-bundles') || !$this->user()->can('delete-bundles')) {
            abort(403);
        }

        $report = BundlesImportReport::find($id);

        BundlesImportReportService::destroy($report);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('create-bundles') || !$this->user()->can('update-bundles') || !$this->user()->can('delete-bundles')) {
            abort(403);
        }

        return view('livewire.bundles-import-report.bundles-import-report-index');
    }
}
