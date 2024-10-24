<?php

namespace App\Jobs\Supplier;

use App\Services\EmailSupplierService;
use Box\Spout\Reader\IteratorInterface;
use Box\Spout\Reader\XLSX\Sheet;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Iterator;

class ProcessData implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public EmailSupplierService $emailSupplierService, public Collection|Sheet $collection)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->collection instanceof Collection) {
            $this->collection->each(function (Collection $row) {
                $this->emailSupplierService->processData($row);
            });
        } else {
            foreach ($this->collection->getRowIterator() as $row) {
                $this->emailSupplierService->processData(collect($row->toArray()));
            }
        }
    }
}
