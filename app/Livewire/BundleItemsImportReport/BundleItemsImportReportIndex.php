<?php

namespace App\Livewire\BundleItemsImportReport;

use App\Jobs\Bundle\BundleItemsImport;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\BundleItemsImportReport;
use App\Services\Bundle\BundleItemsImportReportService;
use App\Services\Bundle\BundleItemsService;
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

class BundleItemsImportReportIndex extends BaseComponent
{
    use WithSort, WithPagination, WithFileUploads;

    public $file;

    public function getListeners(): array
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'render',
        ];
    }

    public function destroy($id): void
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        $report = BundleItemsImportReport::find($id);

        BundleItemsImportReportService::destroy($report);
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        return \Excel::download(new \App\Exports\BundleItemsExport($this->currentUser(), true), "priceseller_bundles_plural_шаблон.xlsx");
    }

    public function import(): void
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(BundleItemsService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(BundleItemsImport::getUniqueId($this->currentUser()), BundleItemsImport::class);

        if ($status) BundleItemsImport::dispatch($uuid, $ext, $this->currentUser());
    }

    #[Computed]
    public function bundleItemsImportReports(): LengthAwarePaginator
    {
        return $this->tapQuery($this->currentUser()->bundleItemsImportReports());

    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('update-bundles')) {
            abort(403);
        }

        return view('livewire.bundle-items-import-report.bundle-items-import-report-index');
    }
}
