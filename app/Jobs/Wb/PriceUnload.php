<?php

namespace App\Jobs\Wb;

use App\Contracts\MarketItemPriceContract;
use App\Contracts\MarketItemStockContract;
use App\Contracts\ReportContract;
use App\Contracts\ReportLogContract;
use App\Enums\ReportStatus;
use App\Models\EmailSupplier;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\Supplier;
use App\Models\WbMarket;
use App\Services\WbItemPriceService;
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
    public ReportLog $log;
    public ReportLogContract $reportLogContract;

    /**
     * Create a new job instance.
     */
    public function __construct(public WbMarket $market, public EmailSupplier|Supplier $supplier, public Report $report)
    {
        $this->queue = 'market-unload';
        $this->reportLogContract = app(ReportLogContract::class);
        $this->log = $this->reportLogContract->new($this->report, "Выгрузка кабинета: {$this->market->name}");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->reportLogContract->running($this->log);
        $actualSupplier = $this->supplier instanceof EmailSupplier ? $this->supplier->supplier : $this->supplier;
        $warehouses = $this->supplier instanceof EmailSupplier ?
            $this->supplier->warehouses->pluck('supplier_warehouse_id')->values()->toArray() :
            $this->supplier->warehouses->pluck('id')->values()->toArray();

        // Обновление цен
        $servicePrice = app(MarketItemPriceContract::class);
        $servicePrice->make($actualSupplier, $this->market, $this->log);
        $servicePrice->updatePrice();
        $servicePrice->unloadAllPrices();

        // Обновление остатков
        $serviceStock = app(MarketItemStockContract::class);
        $serviceStock->make($actualSupplier, $this->market, $this->log, $warehouses);
        $serviceStock->updateStock();
        $serviceStock->unloadAllStocks();

        $this->reportLogContract->finished($this->log);

    }

    public function failed(\Throwable $th): void
    {
        $this->reportLogContract->failed($this->log);
    }

    public function uniqueId(): string
    {
        return $this->market->id . $this->supplier->id . "wb_price_unload";
    }
}
