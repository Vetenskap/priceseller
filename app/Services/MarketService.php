<?php

namespace App\Services;

use App\Contracts\MarketContract;
use App\Helpers\Helpers;
use App\Jobs\MarketUpdateApiCommissions;
use App\Models\EmailSupplier;
use App\Models\OzonMarket;
use App\Models\Report;
use App\Models\Supplier;
use App\Models\WbMarket;
use Illuminate\Bus\Batch;

class MarketService implements MarketContract
{
    public static function updateCommissionsInTime(): void
    {
        $time = now();

        OzonMarket::where('open', true)
            ->where('close', false)
            ->where('enabled_update_commissions_in_time', true)
            ->get()
            ->each(function (OzonMarket $market) use ($time) {
                if ($market->update_commissions_time === $time->timezone(Helpers::getUserTimeZone($market->user))->format('H:i')) {
                    MarketUpdateApiCommissions::dispatch($market, OzonMarketService::class);
                }
            });

        WbMarket::where('open', true)
            ->where('close', false)
            ->where('enabled_update_commissions_in_time', true)
            ->get()
            ->each(function (WbMarket $market) use ($time) {
                if ($market->update_commissions_time === $time->timezone(Helpers::getUserTimeZone($market->user))->format('H:i')) {
                    MarketUpdateApiCommissions::dispatch($market, WbMarketService::class);
                }
            });
    }

    public function unload(EmailSupplier|Supplier $supplier, Report $report): void
    {
        $currentSupplier = $supplier instanceof Supplier ? $supplier : $supplier->supplier;

        Helpers::toBatch(function ($batch) use ($currentSupplier, $supplier, $report) {

            $currentSupplier->user->ozonMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(OzonMarket $market) => $market->suppliers()->where('id', $currentSupplier->id)->first())
                ->each(function (OzonMarket $market) use ($batch, $supplier, $report) {
                    $batch->add(new \App\Jobs\Ozon\PriceUnload($market, $supplier, $report));
                });

            $currentSupplier->user->wbMarkets()
                ->where('open', true)
                ->where('close', false)
                ->get()
                ->filter(fn(WbMarket $market) => $market->suppliers()->where('id', $currentSupplier->id)->first())
                ->each(function (WbMarket $market) use ($batch, $supplier, $report) {
                    $batch->add(new \App\Jobs\Wb\PriceUnload($market, $supplier, $report));
                });
        }, 'market-unload', function () use ($report): bool {
            $report = $report->fresh();
            return $report->isCancelled();
        });
    }
}
