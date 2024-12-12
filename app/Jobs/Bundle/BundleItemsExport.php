<?php

namespace App\Jobs\Bundle;

use App\Models\User;
use App\Services\Bundle\BundleItemsExportReportService;
use App\Services\Bundle\BundleItemsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BundleItemsExport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public function __construct(public User $user)
    {
        $this->queue = 'export-or-import';
        BundleItemsExportReportService::new($this->user);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new BundleItemsService($this->user);
        $uuid = $service->exportItems();
        BundleItemsExportReportService::success($this->user, $uuid);
    }

    public function failed(): void
    {
        BundleItemsExportReportService::error($this->user);
    }

    public function uniqueId(): string
    {
        return $this->user->id . 'export_bundle_items';
    }

    public static function getUniqueId(User $user): string
    {
        return $user->id . 'export_bundle_items';
    }
}
