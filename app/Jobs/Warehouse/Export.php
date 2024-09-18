<?php

namespace App\Jobs\Warehouse;

use App\Exports\WarehousesStocksExport;
use App\Models\User;
use App\Services\WarehouseItemsExportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class Export implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (WarehouseItemsExportReportService::newOrFail($this->user)) {
            $uuid = Str::uuid();
            \Excel::store(new WarehousesStocksExport($this->user), 'users/warehouses/' . $uuid . '.xlsx', 'public');
            WarehouseItemsExportReportService::success($this->user, $uuid);
        }
    }

    public function failed(\Throwable $e): void
    {
        WarehouseItemsExportReportService::error($this->user);
    }

    public function uniqueId(): string
    {
        return $this->user->id . 'export_warehouse';
    }

    public static function getUniqueId(User $user): string
    {
        return $user->id . 'export_warehouse';
    }
}
