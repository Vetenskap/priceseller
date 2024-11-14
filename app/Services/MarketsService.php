<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\Jobs\MarketUpdateApiCommissions;
use App\Models\OzonMarket;
use App\Models\WbMarket;

class MarketsService
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
}
