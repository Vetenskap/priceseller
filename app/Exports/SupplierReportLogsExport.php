<?php

namespace App\Exports;

use App\Models\SupplierReport;
use App\Models\SupplierReportLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SupplierReportLogsExport implements FromCollection, WithHeadings
{
    public function __construct(public SupplierReport $report)
    {
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->report->logs->sortByDesc('id')->map(function (SupplierReportLog $log) {
            return [
                'date' => $log->created_at,
                'message' => $log->message
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Дата',
            'Сообщение'
        ];
    }
}
