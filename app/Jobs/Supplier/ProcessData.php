<?php

namespace App\Jobs\Supplier;

use App\Services\EmailSupplierEmailService;
use Box\Spout\Reader\XLSX\Sheet;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class ProcessData implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public EmailSupplierEmailService $emailSupplierService, public Collection $collection)
    {
        $this->queue = 'supplier-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $this->collection->each(function (Collection $row) {
            $this->emailSupplierService->processData($row);
        });
    }
}
