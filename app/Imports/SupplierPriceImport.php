<?php

namespace App\Imports;

use App\Jobs\Supplier\ProcessData;
use App\Services\EmailSupplierService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class SupplierPriceImport implements ToCollection, WithChunkReading
{

    public function __construct(protected EmailSupplierService $emailSupplierService, protected Batch $batch) {}

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        logger(memory_get_usage() / 1024 / 1024 . 'MB memory usage');

        while (memory_get_usage() > $this->emailSupplierService->limitMemory) {
            sleep(20);
        }

        $this->batch->add(new ProcessData($this->emailSupplierService, $collection));
    }

    public function chunkSize(): int
    {
        return 10000;
    }

}
