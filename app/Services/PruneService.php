<?php

namespace App\Services;

class PruneService
{
    public static function reports(): void
    {
        ItemsExportReportService::prune();
        ItemsImportReportService::prune();
        SupplierReportService::prune();
        WarehouseItemsExportReportService::prune();
        WarehouseItemsImportReportService::prune();
    }
}
