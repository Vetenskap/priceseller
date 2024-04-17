<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;

class SupplierImport implements ToCollection, WithHeadingRow, WithChunkReading, WithProgressBar
{
    use Importable;

    public function collection(Collection $rows)
    {
        logger($rows->all());
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
