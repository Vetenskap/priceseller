<?php

namespace Modules\VoshodApi\Jobs;

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
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Services\VoshodUnloadService;

class VoshodUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public VoshodApi $voshodApi)
    {
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->voshodApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->voshodApi->supplier, message: 'по АПИ');
        }

        $service = new VoshodUnloadService($this->voshodApi);
        $service->getNewPrice();

        $user = $this->voshodApi->user;
        $supplier = $this->voshodApi->supplier;

        Helpers::toBatch(function (Batch $batch) use ($user, $supplier) {

            $user->ozonMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $supplier->id)->first())
                ->each(function (OzonMarket $market) use ($batch, $supplier) {
                    $batch->add(new \App\Jobs\Ozon\PriceUnload($market, $supplier));
                });

            $user->wbMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $supplier->id)->first())
                ->each(function (WbMarket $market) use ($batch, $supplier) {
                    $batch->add(new \App\Jobs\Wb\PriceUnload($market, $supplier));
                });
        }, 'market-unload');

        SupplierReportService::success($supplier, 'по АПИ');
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->voshodApi->supplier, message: 'по АПИ');
    }


}
