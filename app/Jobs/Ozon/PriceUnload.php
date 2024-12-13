<?php

namespace App\Jobs\Ozon;

use App\Contracts\ReportContract;
use App\Contracts\ReportLogContract;
use App\Enums\ReportStatus;
use App\Models\EmailSupplier;
use App\Models\OzonMarket;
use App\Models\Report;
use App\Models\ReportLog;
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
    public ReportLog $log;
    public ReportLogContract $reportLogContract;

    /**
     * Create a new job instance.
     */
    public function __construct(public OzonMarket $market, public EmailSupplier|Supplier $supplier, public Report $report)
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

        $service = new OzonItemPriceService($actualSupplier, $this->market, $warehouses, $this->log);
        $service->updatePrice();
        $service->unloadAllPrices();
        $service->updateStock();
        $service->unloadAllStocks();

        $this->reportLogContract->finished($this->log);

    }

    public function failed(\Throwable $th): void
    {
        $this->reportLogContract->failed($this->log);
    }

    public function uniqueId(): string
    {
        return $this->market->id . $this->supplier->id . "ozon_price_unload";
    }
}
