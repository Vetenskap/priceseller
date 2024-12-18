<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\OzonItem;
use App\Models\ReportLog;
use App\Models\SupplierReportLogMarket;
use App\Models\WbItem;

class SupplierReportLogMarketService
{
    public static function new(ReportLog $log, string $message, OzonItem|WbItem|null $item = null): ?SupplierReportLogMarket
    {
        if ($log->loadExists('supplierReportLogMarkets')) {
            return $log->supplierReportLogMarkets()->create([
                'message' => $message,
                'status' => ReportStatus::running,
                'logable_type' => $item ? get_class($item) : null,
                'logable_id' => $item?->getKey()
            ]);
        }

        return null;
    }

    public static function prune(): int
    {
        // TODO: Implement prune() method.
    }

    public static function timeout(): int
    {
        // TODO: Implement timeout() method.
    }

    public static function failed(SupplierReportLogMarket $log): bool
    {
        return $log->update([
            'status' => ReportStatus::failed
        ]);
    }

    public static function cancelled(SupplierReportLogMarket $log): bool
    {
        return $log->update([
            'status' => ReportStatus::cancelled
        ]);
    }

    public static function finished(SupplierReportLogMarket $log): bool
    {
        return $log->update([
            'status' => ReportStatus::finished
        ]);
    }

    public static function running(SupplierReportLogMarket $log): bool
    {
        return $log->update([
            'status' => ReportStatus::running
        ]);
    }
}
