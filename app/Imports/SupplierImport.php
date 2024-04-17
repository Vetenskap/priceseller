<?php

namespace App\Imports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    use Importable;

    private Supplier $supplier;

    public function __construct(string $supplierId)
    {
        $this->supplier = Supplier::findOrFail($supplierId);
    }

    public function collection(Collection $rows)
    {
        logger($rows->all());
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
