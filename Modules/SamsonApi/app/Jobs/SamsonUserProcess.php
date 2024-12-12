<?php

namespace Modules\SamsonApi\Jobs;

use App\Helpers\Helpers;
use App\Models\OzonMarket;
use App\Models\WbMarket;
use App\Services\SupplierReportService;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\SamsonApi\Models\SamsonApi;
use Modules\SamsonApi\Services\SamsonUnloadService;

class SamsonUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    /**
     * Create a new job instance.
     */
    public function __construct(public SamsonApi $samsonApi)
    {
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->samsonApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->samsonApi->supplier, message: 'по АПИ');
        }

        $service = new SamsonUnloadService();
        $service->getNewPrice($this->samsonApi);

        $user = $this->samsonApi->user;
        $supplier = $this->samsonApi->supplier;

        Helpers::toBatch(function (Batch $batch) use ($user, $supplier) {

            $user->ozonMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $supplier)->first())
                ->each(function (OzonMarket $market) use ($batch, $supplier) {
                    $batch->add(new \App\Jobs\Ozon\PriceUnload($market, $supplier));
                });

            $user->wbMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $supplier)->first())
                ->each(function (WbMarket $market) use ($batch, $supplier) {
                    $batch->add(new \App\Jobs\Wb\PriceUnload($market, $supplier));
                });
        }, 'market-unload');

        SupplierReportService::success($this->samsonApi->supplier, message: 'по АПИ');
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->samsonApi->supplier, message: 'по АПИ');
    }
}
