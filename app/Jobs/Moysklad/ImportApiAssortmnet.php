<?php

namespace App\Jobs\Moysklad;

use App\Models\Moysklad;
use App\Services\MoyskladService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ImportApiAssortmnet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Moysklad $moysklad, public Collection $attributes, public int $offset, public int $limit)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new MoyskladService($this->moysklad);
        $service->setClient();
        $service->importItemsApi($this->attributes, $this->offset, $this->limit);
    }
}
