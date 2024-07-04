<?php

namespace App\Jobs\Warehouse;

use App\Imports\WarehousesStocksImport;
use App\Models\User;
use App\Services\WarehouseItemsImportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Import implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public string $uuid)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        WarehouseItemsImportReportService::newOrFail($this->user, $this->uuid);
        \Excel::import(new WarehousesStocksImport($this->user), 'users/warehouses/' . $this->uuid . '.xlsx');
        WarehouseItemsImportReportService::success($this->user, 0, 0);
    }

    public function failed(\Throwable $e)
    {
        WarehouseItemsImportReportService::error($this->user);
    }
}
