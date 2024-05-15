<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\SupplierReport;

class SupplierReportService
{
    public static function get(Supplier $supplier): ?SupplierReport
    {
        return $supplier->reports()->where('status', 2)->first();
    }

    public static function new(Supplier $supplier, string $path): void
    {
        if (static::get($supplier)) {
            dd('Поставщик уже выгружается');
        } else {
            $supplier->reports()->create([
                'status' => 2,
                'message' => 'Начало выгрузки',
                'path' => $path
            ]);
        }
    }

    public static function changeMessage(Supplier $supplier, string $message): void
    {
        if ($report = static::get($supplier)) {
            $report->message = $message;
            $report->save();
        } else {
            dd('В данный момент поставщик не выгружается');
        }
    }

    public static function success(Supplier $supplier): void
    {
        if ($report = static::get($supplier)) {
            $report->message = 'Поставщик успешно выгружен';
            $report->status = 0;
            $report->save();
        } else {
            dd('В данный момент поставщик не выгружается');
        }
    }

    public static function error(Supplier $supplier): void
    {
        if ($report = static::get($supplier)) {
            $report->message = 'Ошибка в выгрузке';
            $report->status = 1;
            $report->save();
        } else {
            dd('В данный момент поставщик не выгружается');
        }
    }
}
