<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Import implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uniqueFor = 10700;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $uuid, public string $ext, public OzonMarket|WbMarket|User|Warehouse $model, public string $service)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (ItemsImportReportService::newOrFail($this->model, $this->uuid)) {
            if (class_exists($this->service)) {

                $service = new $this->service($this->model);

                if (method_exists($service, 'importItems')) {

                    $result = $service->importItems($this->uuid, $this->ext);

                    ItemsImportReportService::success(
                        model: $this->model,
                        correct: $result->get('correct', 0),
                        error: $result->get('error', 0),
                        updated: $result->get('updated', 0),
                        deleted: $result->get('deleted', 0),
                        uuid: $this->uuid
                    );
                }
            }
        }
    }

    public function failed()
    {
        ItemsImportReportService::error($this->model);
    }

    public function uniqueId(): string
    {
        return $this->model->id;
    }
}
