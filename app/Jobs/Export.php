<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\ItemsExportReportService;
use App\Services\ItemsImportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Export implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uniqueFor = 10700;

    /**
     * Create a new job instance.
     */
    public function __construct(public OzonMarket|WbMarket|User|Warehouse $model, public string $service)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (ItemsExportReportService::newOrFail($this->model)) {
            if (class_exists($this->service)) {
                $service = new $this->service($this->model);
                if (method_exists($service, 'exportItems')) {
                    $uuid = $service->exportItems();

                    ItemsExportReportService::success($this->model, $uuid);
                }
            }
        }
    }

    public function failed()
    {
        ItemsExportReportService::error($this->model);
    }

    public function uniqueId(): string
    {
        return $this->model->id;
    }
}
