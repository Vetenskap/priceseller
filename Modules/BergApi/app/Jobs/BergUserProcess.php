<?php

namespace Modules\BergApi\Jobs;

use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
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

    /**
     * Create a new job instance.
     */
    public function __construct(public BergApi $bergApi)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (SupplierReportService::get($this->bergApi->supplier)) {
            return;
        } else {
            SupplierReportService::new($this->bergApi->supplier);
        }

        $service = new BergUnloadService($this->bergApi);
        $service->getNewPrice();

        $user = $this->bergApi->user;
        $supplier = $this->bergApi->supplier;

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

        SupplierReportService::success($this->bergApi->supplier);
    }

    public function failed(\Throwable $th)
    {
        SupplierReportService::error($this->bergApi->supplier);
    }
}
