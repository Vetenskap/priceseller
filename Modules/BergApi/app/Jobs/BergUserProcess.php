<?php

namespace Modules\BergApi\Jobs;

use App\Helpers\Helpers;
use App\Jobs\Supplier\MarketsUnload;
use App\Models\OzonMarket;
use App\Models\WbMarket;
use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\BergApi\Models\BergApi;
use Modules\BergApi\Services\BergUnloadService;

class BergUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    /**
     * Create a new job instance.
     */
    public function __construct(public BergApi $bergApi)
    {
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->bergApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->bergApi->supplier, message: 'по АПИ');
        }

        $service = new BergUnloadService($this->bergApi);
        $service->getNewPrice();

        $user = $this->bergApi->user;
        $supplier = $this->bergApi->supplier;

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

        SupplierReportService::success($this->bergApi->supplier, message: 'по АПИ');

    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->bergApi->supplier, message: 'по АПИ');
    }
}
