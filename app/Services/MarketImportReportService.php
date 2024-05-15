<?php

namespace App\Services;

use App\Models\MarketImportReport;
use App\Models\OzonMarket;
use App\Models\WbMarket;

class MarketImportReportService
{
    public static function get(OzonMarket|WbMarket $market)
    {
        return $market->importReports()->where('status', 2)->first();
    }

    public static function newOrFirst(OzonMarket|WbMarket $market): MarketImportReport
    {
        if ($report = static::get($market)) {
            return $report;
        } else {
            return $market->importReports()->create([
                'status' => 2,
                'message' => 'В процессе'
            ]);
        }
    }

    public static function success(OzonMarket|WbMarket $market, int $correct, int $error, ?string $uuid = null)
    {
        if ($report = static::get($market)) {
            $report->update([
                'uuid' => $uuid,
                'correct' => $correct,
                'error' => $error,
                'status' => 0,
                'message' => 'Импорт завершен'
            ]);
        } else {
            dd('В данный момент нет активных импортов');
        }
    }
}
