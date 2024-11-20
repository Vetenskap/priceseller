<?php

namespace App\Jobs;

use App\Models\OzonMarket;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbMarket;
use App\Services\Item\ItemService;
use App\Services\ItemsExportReportService;
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

class Export implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 600;

    public function __construct(public OzonMarket|WbMarket|User|Warehouse $model, public string $service)
    {
        ItemsExportReportService::new($this->model);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (class_exists($this->service)) {

            /** @var ItemService|OzonMarketService|WbMarketService|WarehouseService $service */
            $service = new $this->service($this->model);
            if (method_exists($service, 'exportItems')) {
                $uuid = $service->exportItems();

                ItemsExportReportService::success($this->model, $uuid);
            } else {
                $this->fail('Method not found: ' . $this->service);
            }
        } else {
            $this->fail('Class not found: ' . $this->service);
        }
    }

    public function failed(): void
    {
        ItemsExportReportService::error($this->model);
    }

    public function uniqueId(): string
    {
        return $this->model->id . 'export';
    }

    public static function getUniqueId(OzonMarket|WbMarket|User|Warehouse $model): string
    {
        return $model->id . 'export';
    }
}
