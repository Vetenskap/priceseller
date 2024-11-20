<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\Item\ItemService;
use App\Services\ItemsImportReportService;
use App\Services\OzonMarketService;
use App\Services\WarehouseService;
use App\Services\WbMarketService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Import implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $uuid, public string $ext, public OzonMarket|WbMarket|User|Warehouse $model, public string $service)
    {
        ItemsImportReportService::new($this->model, $this->uuid);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (class_exists($this->service)) {

            /** @var ItemService|OzonMarketService|WbMarketService|WarehouseService $service */
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
            } else {
                $this->fail('Method not found');
            }
        } else {
            $this->fail('Service not found: ' . $this->service);
        }
    }

    public function failed(): void
    {
        ItemsImportReportService::error($this->model);
    }

    public function uniqueId(): string
    {
        return $this->model->id . 'import';
    }

    public static function getUniqueId(OzonMarket|WbMarket|User|Warehouse $model): string
    {
        return $model->id . 'import';
    }
}
