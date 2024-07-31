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

    public $uniqueFor = 10700;
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
        if (WarehouseItemsImportReportService::newOrFail($this->user, $this->uuid)) {
            $import = new WarehousesStocksImport($this->user);
            \Excel::import($import, 'users/warehouses/' . $this->uuid . '.xlsx');
            WarehouseItemsImportReportService::success($this->user, $import->correct, $import->error);
        }
    }

    public function failed(\Throwable $e)
    {
        WarehouseItemsImportReportService::error($this->user);
    }


    public function uniqueId(): string
    {
        return $this->user->id;
    }
}
