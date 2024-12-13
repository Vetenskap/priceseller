<?php

namespace App\Jobs\Ozon;

use App\Contracts\ReportContract;
use App\Models\EmailSupplier;
use App\Models\OzonMarket;
use App\Models\Report;
use App\Models\Supplier;
use App\Services\OzonItemPriceService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PriceUnload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $uniqueFor = 7200;
    public ReportContract $reportContract;

    /**
     * Create a new job instance.
     */
    public function __construct(public OzonMarket $market, public EmailSupplier|Supplier $supplier, public Report $report)
    {
        $this->queue = 'market-unload';
        $this->reportContract = app(ReportContract::class);
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

        $service = new OzonItemPriceService($actualSupplier, $this->market, $warehouses);
        $service->updatePrice();
        $service->unloadAllPrices();
        $service->updateStock();
        $service->unloadAllStocks();

    }

    public function uniqueId(): string
    {
        return $this->market->id . $this->supplier->id . "ozon_price_unload";
    }
}
