<?php

namespace App\Jobs\Warehouse;

use App\Exports\WarehousesStocksExport;
use App\Models\User;
use App\Services\WarehouseItemsExportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class Export implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    public function failed(\Throwable $e)
    {
        WarehouseItemsExportReportService::error($this->user);
    }

//    public function uniqueId(): string
//    {
//        return $this->user->id;
//    }
}
