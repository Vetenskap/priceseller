<?php

namespace App\Jobs\Supplier;

use App\Models\Supplier;
use App\Models\User;
use App\Services\OzonItemPriceService;
use App\Services\SupplierReportService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarketsUnload implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;
    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public Supplier $supplier)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ozonMarkets = $this->user->ozonMarkets()
            ->whereHas('items', function (Builder $query) {
                $query->whereHas('item', function (Builder $query) {
                    $query->where('supplier_id', $this->supplier->id);
                });
            })
            ->where('open', true)
            ->where('close', false)
            ->get();

        $wbMarkets = $this->user->wbMarkets()
            ->whereHas('items', function (Builder $query) {
                $query->whereHas('item', function (Builder $query) {
                    $query->where('supplier_id', $this->supplier->id);
                });
            })
            ->where('open', true)
            ->where('close', false)
            ->get();

        foreach ($ozonMarkets as $market) {
            $service = new OzonItemPriceService($this->supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        foreach ($wbMarkets as $market) {
            $service = new WbItemPriceService($this->supplier, $market);
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        SupplierReportService::success($this->supplier);
    }

    public function failed(\Throwable $th): void
    {
        SupplierReportService::error($this->supplier);
    }

    public function uniqueId(): string
    {
        return $this->supplier->id . 'markets_unload';
    }
}
