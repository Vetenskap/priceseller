<?php

namespace App\Imports;

use App\Services\EmailSupplierService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SupplierPriceImport implements ToCollection, WithChunkReading
{

    public function __construct(protected EmailSupplierService $emailSupplierService) {}

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $emailSupplierService = $this->emailSupplierService;

        dispatch(function () use ($collection, $emailSupplierService) {
            $collection->each(function (Collection $row) use ($emailSupplierService) {
                $emailSupplierService->processData($row);
            });
        })->onQueue('email-supplier-unload');
    }

    public function chunkSize(): int
    {
        return 10000;
    }

}
