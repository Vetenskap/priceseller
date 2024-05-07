<?php

namespace App\Imports;

use App\Models\EmailSupplier;
use App\Models\Supplier;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SupplierImport implements ToCollection, WithHeadingRow, WithChunkReading, WithProgressBar, ShouldQueue
{
    use Importable;

    private EmailSupplier $emailSupplier;

    public function __construct(string $emailSupplierId)
    {
        $this->emailSupplier = EmailSupplier::findOrFail($emailSupplierId);
    }

    public function collection(Collection $rows)
    {
        dd($rows);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return $this->emailSupplier->header_start;
    }
}
