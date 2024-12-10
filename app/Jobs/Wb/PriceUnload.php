<?php

namespace App\Jobs\Wb;

use App\Models\EmailSupplier;
use App\Models\Supplier;
use App\Models\WbMarket;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PriceUnload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    /**
     * Create a new job instance.
     */
    public function __construct(public WbMarket $market, public EmailSupplier|Supplier $supplier)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $actualSupplier = $this->supplier instanceof EmailSupplier ? $this->supplier->supplier : $this->supplier;
        $warehouses = $this->supplier instanceof EmailSupplier ?
            $this->supplier->warehouses->pluck('supplier_warehouse_id')->values()->toArray() :
            $this->supplier->warehouses->pluck('id')->values()->toArray();

        $service = new WbItemPriceService($actualSupplier, $this->market, $warehouses);
        $service->updateStock();
        $service->updatePrice();
        $service->unloadAllStocks();
        $service->unloadAllPrices();
    }
}
