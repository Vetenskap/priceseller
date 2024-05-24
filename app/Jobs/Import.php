<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\User;
use App\Models\WbMarket;
use App\Services\ItemsImportReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Import implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $uuid, public string $ext, public OzonMarket|WbMarket|User $model, public string $service)
    {
        if (!ItemsImportReportService::new($this->model, $this->uuid)) {
            throw new \Exception("Уже идёт импорт");
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if (class_exists($this->service)) {
            $service = new $this->service($this->model);

            if (method_exists($service, 'importItems')) {
                $result = $service->importItems($this->uuid, $this->ext);

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
