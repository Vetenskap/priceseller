<?php

namespace Modules\Moysklad\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladItemsApiImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;
    /**
     * Create a new job instance.
     */
    public function __construct(public Moysklad $moysklad)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new MoyskladService($this->moysklad);
        $service->importApiItems();
    }

    public function uniqueId(): string
    {
        return $this->moysklad->id . 'items_api_import';
    }
}
