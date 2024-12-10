<?php

namespace App\Jobs\Wb;

use App\Models\EmailSupplier;
use App\Models\WbMarket;
use App\Services\WbItemPriceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PriceUnload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, \Illuminate\Bus\Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct(public WbMarket $market, public EmailSupplier $emailSupplier)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new WbItemPriceService($this->emailSupplier->supplier, $this->market, $this->emailSupplier->warehouses->pluck('supplier_warehouse_id')->values()->toArray());
        $service->updateStock();
        $service->updatePrice();
        $service->unloadAllStocks();
        $service->unloadAllPrices();
    }
}
