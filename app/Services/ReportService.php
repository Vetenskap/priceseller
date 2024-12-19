<?php

namespace App\Services;

class ReportService
{
    public static function checkTimeouts(): void
    {
        ItemsExportReportService::timeout();
        ItemsImportReportService::timeout();
        WarehouseItemsImportReportService::timeout();
        WarehouseItemsExportReportService::timeout();
    }
}
