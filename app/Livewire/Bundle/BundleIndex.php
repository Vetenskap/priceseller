<?php

namespace App\Livewire\Bundle;

use App\Exports\BundlesExport;
use App\Exports\ItemsExport;
use App\Jobs\Bundle\BundleItemsExport;
use App\Jobs\Bundle\BundleItemsImport;
use App\Jobs\Bundle\BundlesImport;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\BundleItemsExportReport;
use App\Models\BundleItemsImportReport;
use App\Models\BundlesExportReport;
use App\Models\BundlesImportReport;
use App\Models\User;
use App\Services\Bundle\BundleItemsExportReportService;
use App\Services\Bundle\BundleItemsImportReportService;
use App\Services\Bundle\BundleItemsService;
use App\Services\Bundle\BundleService;
use App\Services\Bundle\BundlesExportReportService;
use App\Services\Bundle\BundlesImportReportService;
use App\Services\Item\ItemService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('Комплекты')]
class BundleIndex extends BaseComponent
{
    use WithFileUploads, WithJsNotifications, WithFilters;

    /** @var TemporaryUploadedFile $file */
    public $file;

    public User $user;

    public $page;

    public function mount($page = 'list'): void
    {
        $this->user = auth()->user();
        $this->page = $page;
    }

    public function downloadBundleExport(int $id)
    {
        $report = BundlesExportReport::find($id);

        return BundlesExportReportService::download($report);
    }

    public function destroyBundleExport(int $id)
    {
        $report = BundlesExportReport::find($id);

        BundlesExportReportService::destroy($report);
    }

    public function destroyBundleImport(int $id)
    {
        $report = BundlesImportReport::find($id);

        BundlesImportReportService::destroy($report);
    }

    public function downloadBundlesTemplate(): BinaryFileResponse
    {
        return \Excel::download(new BundlesExport($this->user, true), "priceseller_bundles_шаблон.xlsx");
    }

    public function exportBundles(): void
    {
        $status = $this->checkTtlJob(\App\Jobs\Bundle\BundlesExport::getUniqueId($this->user), \App\Jobs\Bundle\BundlesExport::class);

        if ($status) \App\Jobs\Bundle\BundlesExport::dispatch($this->user);
    }

    public function importBundles(): void
    {
        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(BundleService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(BundlesImport::getUniqueId($this->user), BundlesImport::class);

        if ($status) BundlesImport::dispatch($uuid, $ext, $this->user);
    }

    public function downloadBundleItemsExport(int $id)
    {
        $report = BundleItemsExportReport::find($id);

        return BundleItemsExportReportService::download($report);
    }

    public function destroyBundleItemsExport(int $id)
    {
        $report = BundleItemsExportReport::find($id);

        BundleItemsExportReportService::destroy($report);
    }

    public function destroyBundleItemsImport(int $id)
    {
        $report = BundleItemsImportReport::find($id);

        BundleItemsImportReportService::destroy($report);
    }

    public function downloadBundleItemsTemplate(): BinaryFileResponse
    {
        return \Excel::download(new \App\Exports\BundleItemsExport($this->user, true), "priceseller_bundles_plural_шаблон.xlsx");
    }

    public function exportBundleItems(): void
    {
        $status = $this->checkTtlJob(BundleItemsExport::getUniqueId($this->user), BundleItemsExport::class);

        if ($status) BundleItemsExport::dispatch($this->user);
    }

    public function importBundleItems(): void
    {
        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(BundleItemsService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(BundleItemsImport::getUniqueId($this->user), BundleItemsImport::class);

        if ($status) BundleItemsImport::dispatch($uuid, $ext, $this->user);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->page === 'list') {

            $bundles = auth()
                ->user()
                ->bundles()
                ->orderByDesc('updated_at')
                ->with('items')
                ->paginate(10);

            return view('livewire.bundle.pages.bundle-list-page', [
                'bundles' => $bundles
            ]);

        } else if ($this->page === 'manage') {
            return view('livewire.bundle.pages.bundle-manage-page', [
                'bundlesExportReports' => auth()->user()->bundlesExportReports,
                'bundlesImportReports' => auth()->user()->bundlesImportReports
            ]);
        } else if ($this->page === 'plural') {
            return view('livewire.bundle.pages.bundle-plural-page', [
                'bundleItemsExportReports' => auth()->user()->bundleItemsExportReports,
                'bundleItemsImportReports' => auth()->user()->bundleItemsImportReports
            ]);
        }

        return view('livewire.bundle.bundle-index');
    }
}
