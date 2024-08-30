<?php

namespace App\Jobs\Bundle;

use App\Models\User;
use App\Services\Bundle\BundleService;
use App\Services\Bundle\BundlesExportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BundlesExport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public function __construct(public User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (BundlesExportReportService::new($this->user)) {
            $service = new BundleService($this->user);
            $uuid = $service->exportItems();
            BundlesExportReportService::success($this->user, $uuid);
        }
    }

    public function failed(): void
    {
        BundlesExportReportService::error($this->user);
    }

    public function uniqueId(): string
    {
        return $this->user->id . 'export_bundles';
    }
}
