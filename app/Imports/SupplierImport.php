<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SupplierImport implements ToCollection, WithHeadingRow, WithChunkReading, WithProgressBar, ShouldQueue
{
    use Importable;

    private Supplier $supplier;

    public function __construct(string $supplierId)
    {
        $this->supplier = Supplier::findOrFail($supplierId);
    }

    public function collection(Collection $rows)
    {
        dump($rows);
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return 9;
    }
}
