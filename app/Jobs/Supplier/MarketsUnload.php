<?php

namespace App\Jobs\Supplier;

use App\Models\Bundle;
use App\Models\Item;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbMarket;
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
    public function __construct(public User $user, public Supplier $supplier, public ?string $message = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ozonMarkets = $this->user->ozonMarkets()
            ->where('open', true)
            ->where('close', false)
            ->get()
            ->filter(fn (OzonMarket $market) => $market->suppliers()->where('id', $this->supplier->id)->first());

        $wbMarkets = $this->user->wbMarkets()
            ->where('open', true)
            ->where('close', false)
            ->get()
            ->filter(fn (WbMarket $market) => $market->suppliers()->where('id', $this->supplier->id)->first());

        foreach ($ozonMarkets as $market) {
            $service = new OzonItemPriceService($this->supplier, $market, $this->supplier->warehouses->pluck('id')->values()->toArray());
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        foreach ($wbMarkets as $market) {
            $service = new WbItemPriceService($this->supplier, $market, $this->supplier->warehouses->pluck('id')->values()->toArray());
            $service->updateStock();
            $service->updatePrice();
            $service->unloadAllStocks();
            $service->unloadAllPrices();
        }

        SupplierReportService::success($this->supplier, message: $this->message);
    }

    public function failed(\Throwable $th): void
    {
        SupplierReportService::error($this->supplier, message: $this->message);
    }

    public function uniqueId(): string
    {
        return $this->supplier->id . 'markets_unload';
    }

    public static function getUniqueId(Supplier $supplier): string
    {
        return $supplier->id . 'markets_unload';
    }
}
