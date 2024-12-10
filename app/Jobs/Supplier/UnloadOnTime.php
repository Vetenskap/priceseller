<?php

namespace App\Jobs\Supplier;

use App\Helpers\Helpers;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\WbMarket;
use App\Services\SupplierReportService;
use App\Services\SupplierService;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UnloadOnTime implements ShouldQueue
{
    use Queueable;

    public int $uniqueFor = 600;
    public int $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(public Supplier $supplier)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (app(SupplierReportService::class)->get($this->supplier)) {
            return;
        } else {
            app(SupplierReportService::class)->new($this->supplier);
        }

        \app(SupplierService::class)->setAllItemsUpdated($this->supplier);
        Helpers::toBatch(function (Batch $batch) {

            $this->supplier->user->ozonMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $this->supplier->id)->first())
                ->each(function (OzonMarket $market) use ($batch) {
                    $batch->add(new \App\Jobs\Ozon\PriceUnload($market, $this->supplier));
                });

            $this->supplier->user->wbMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $this->supplier->id)->first())
                ->each(function (WbMarket $market) use ($batch) {
                    $batch->add(new \App\Jobs\Wb\PriceUnload($market, $this->supplier));
                });
        });

        SupplierReportService::success($this->supplier);
    }

    public function failed(\Throwable $th): void
    {
        app(SupplierReportService::class)->error($this->supplier);
    }

    public function uniqueId(): string
    {
        return $this->supplier->id . "price_unload";
    }
}
