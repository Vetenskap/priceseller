<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MarketRelationshipsAndCommissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Collection $defaultFields, public OzonMarket|WbMarket $model, public string $service)
    {
        ItemsImportReportService::new($this->model, '');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (class_exists($this->service)) {

            $service = new $this->service($this->model);

            if (method_exists($service, 'directRelationships')) {

                $result = $service->directRelationships($this->defaultFields);

                ItemsImportReportService::success(
                    model: $this->model,
                    correct: $result->get('correct', 0),
                    error: $result->get('error', 0),
                );
            }
        }
    }

    public function failed()
    {
        ItemsImportReportService::error($this->model);
    }
}
