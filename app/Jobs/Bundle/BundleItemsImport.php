<?php

namespace App\Jobs\Bundle;

use App\Models\User;
use App\Services\Bundle\BundleItemsImportReportService;
use App\Services\Bundle\BundleItemsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BundleItemsImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;
    /**
     * Create a new job instance.
     */
    public function __construct(public string $uuid, public string $ext, public User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (BundleItemsImportReportService::new($this->user, $this->uuid)) {
            $service = new BundleItemsService($this->user);
            $result = $service->importItems($this->uuid, $this->ext);

            BundleItemsImportReportService::success(
                user: $this->user,
                correct: $result->get('correct', 0),
                error: $result->get('error', 0),
                updated: $result->get('updated', 0),
                deleted: $result->get('deleted', 0),
                uuid: $this->uuid
            );
        }
    }

    public function failed(): void
    {
        BundleItemsImportReportService::error($this->user);
    }

    public function uniqueId(): string
    {
        return $this->user->id . 'import_bundle_items';
    }

    public static function getUniqueId(User $user): string
    {
        return $user->id . 'import_bundle_items';
    }
}
