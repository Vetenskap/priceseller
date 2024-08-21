<?php

namespace Modules\SamsonApi\Jobs;

use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\SamsonApi\Models\SamsonApi;
use Modules\SamsonApi\Services\SamsonUnloadService;

class SamsonUserProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public SamsonApi $samsonApi)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->samsonApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->samsonApi->supplier);
        }

        $service = new SamsonUnloadService($this->samsonApi);
        $service->getNewPrice();

        $user = $this->samsonApi->user;
        $supplier = $this->samsonApi->supplier;

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

        SupplierReportService::success($this->samsonApi->supplier);
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->samsonApi->supplier);
    }
}
