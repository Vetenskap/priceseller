<?php

namespace App\Jobs\Market;

use App\Models\Item;
use App\Models\OzonWarehouseStock;
use App\Models\WbWarehouseStock;
use App\Services\OzonItemPriceService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class NullNotUpdatedStocksBatch implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public WbItemPriceService|OzonItemPriceService $service, public Collection $items)
    {
        $this->queue = 'market-update-stock';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->items->filter(function (WbWarehouseStock|OzonWarehouseStock $stock) {

            if ($stock instanceof WbWarehouseStock) {
                $itemable = $stock->wbItem->itemable;
                $type = $stock->wbItem->wbitemable_type;
            } else {
                $itemable = $stock->ozonItem->itemable;
                $type = $stock->ozonItem->ozonitemable_type;
            }

            if ($type === Item::class) {
                if ($itemable->supplier_id === $this->service->supplier->id) {
                    if (!$itemable->unload_wb) {
                        return true;
                    }
                }
            } else {
                if ($itemable->items->every(fn(Item $item) => $item->supplier_id === $this->service->supplier->id)) {
                    if ($itemable->items->first(fn(Item $item) => !$item->unload_wb)) {
                        return true;
                    }
                }
            }

            return false;

        })->each(function (WbWarehouseStock|OzonWarehouseStock $stock) {
            $stock->update(['stock' => 0]);
        });
    }
}
