<?php

namespace Modules\VoshodApi\Jobs;

use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
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
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->voshodApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->voshodApi->supplier);
        }

        $service = new VoshodUnloadService($this->voshodApi);
        $service->getNewPrice();

        $user = $this->voshodApi->user;
        $supplier = $this->voshodApi->supplier;

        $ozonMarkets = $user->ozonMarkets()
            ->whereHas('items', function (Builder $query) use ($supplier) {
                $query->whereHas('item', function (Builder $query) use ($supplier) {
                    $query->where('supplier_id', $supplier->id);
                });
            })
            ->where('open', true)
            ->where('close', false)
            ->get();

        $wbMarkets = $user->wbMarkets()
            ->whereHas('items', function (Builder $query) use ($supplier) {
                $query->whereHas('item', function (Builder $query) use ($supplier) {
                    $query->where('supplier_id', $supplier->id);
                });
            })
            ->where('open', true)
            ->where('close', false)
            ->get();

        foreach ($ozonMarkets as $market) {
            $service = new OzonItemPriceService($supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        foreach ($wbMarkets as $market) {
            $service = new WbItemPriceService($supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        SupplierReportService::success($this->voshodApi->supplier);
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->voshodApi->supplier);
    }
}
