<?php

namespace App\Jobs\Market;

use App\Models\Item;
use App\Models\OzonWarehouseStock;
use App\Models\WbWarehouseStock;
use App\Services\OzonItemPriceService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class NullNotUpdatedStocksBatch implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public WbItemPriceService|OzonItemPriceService $service, public int $offset)
    {
        $this->queue = 'market-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->service instanceof WbItemPriceService) {
            $query = WbWarehouseStock::query()
                ->with('wbItem')
                ->whereHas('wbItem', function (Builder $query) {
                    $query->where('wb_market_id', $this->service->market->id);
                });
        } else {
            $query = OzonWarehouseStock::query()
                ->with('ozonItem')
                ->whereHas('ozonItem', function (Builder $query) {
                    $query->where('ozon_market_id', $this->service->market->id);
                });
        }

        $query->whereHas('warehouse', function (Builder $query) {
                $query->whereHas('suppliers', function (Builder $query) {
                    $query
                        ->where('supplier_id', $this->service->supplier->id)
                        ->when($this->service->supplierWarehousesIds, function (Builder $query) {
                            $query->whereHas('warehouses', function (Builder $query) {
                                $query->whereIn('supplier_warehouse_id', $this->service->supplierWarehousesIds);
                            });
                        });
                });
            })
            ->limit(10000)
            ->offset($this->offset)
            ->get()
            ->filter(function (WbWarehouseStock|OzonWarehouseStock $stock) {

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
