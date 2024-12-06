<?php

namespace App\Jobs\Supplier;

use App\Contracts\ReportContract;
use App\Models\EmailSupplier;
use App\Models\OzonMarket;
use App\Models\Report;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarketsEmailSupplierUnload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public int $tries = 1;

    public ReportContract $reportContract;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public EmailSupplier $emailSupplier, public Report $report)
    {
        $this->reportContract = app(ReportContract::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->report = $this->report->fresh();
        if ($this->report->isCancelled()) return;
        $this->reportContract->changeMessage($this->report, 'Выгрузка в кабинеты');

        $ozonMarkets = $this->user->ozonMarkets()
            ->where('open', true)
            ->where('close', false)
            ->get()
            ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $this->emailSupplier->supplier->id)->first());

        $wbMarkets = $this->user->wbMarkets()
            ->where('open', true)
            ->where('close', false)
            ->get()
            ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $this->emailSupplier->supplier->id)->first());

        foreach ($ozonMarkets as $market) {
            $service = app(OzonItemPriceService::class, [
                'supplier' => $this->emailSupplier->supplier,
                'market' => $market,
                'supplierWarehousesIds' => $this->emailSupplier->warehouses->pluck('supplier_warehouse_id')->values()->toArray(),
            ]);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        foreach ($wbMarkets as $market) {
            $service = new WbItemPriceService($this->emailSupplier->supplier, $market, $this->emailSupplier->warehouses->pluck('supplier_warehouse_id')->values()->toArray());
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        SupplierReportService::success($this->emailSupplier->supplier);

        $this->reportContract->finished($this->report);
    }

    public function failed(\Throwable $th): void
    {
        $this->reportContract->failed($this->report);
    }

    public function uniqueId(): string
    {
        return $this->emailSupplier->id . 'markets_unload';
    }
}
