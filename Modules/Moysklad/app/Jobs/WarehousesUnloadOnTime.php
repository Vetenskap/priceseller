<?php

namespace Modules\Moysklad\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Services\MoyskladWarehouseWarehouseService;

class WarehousesUnloadOnTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Moysklad $moysklad)
    {
        $this->queue = 'moysklad';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->moysklad->warehouses->each(function (MoyskladWarehouseWarehouse $warehouse) {
            $service = new MoyskladWarehouseWarehouseService($warehouse, $this->moysklad);
            $service->updateAllStocks();
        });
    }
}
