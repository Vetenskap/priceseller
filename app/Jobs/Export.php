<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\ItemsExportReportService;
use App\Services\ItemsImportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Export implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public OzonMarket|WbMarket|User $model, public string $service)
    {
        ItemsExportReportService::newOrFirst($this->model);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (class_exists($this->service)) {
            $service = new $this->service($this->model);
            if (method_exists($service, 'exportItems')) {
                $uuid = $service->exportItems();

                ItemsExportReportService::success($this->model, $uuid);
            }
        }
    }

    public function failed()
    {
        ItemsExportReportService::error($this->model);
    }
}
