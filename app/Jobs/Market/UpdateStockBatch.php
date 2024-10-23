<?php

namespace App\Jobs\Market;

use App\Models\Item;
use App\Models\OzonItem;
use App\Models\WbItem;
use App\Services\OzonItemPriceService;
use App\Services\WbItemPriceService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;

class UpdateStockBatch implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public WbItemPriceService|OzonItemPriceService $service, public Collection $items)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->items->filter(function (WbItem|OzonItem $item) {

            if ($item instanceof WbItem) {
                $itemable = $item->wbitemable;
                $type = $item->wbitemable_type;
            } else {
                $itemable = $item->ozonitemable;
                $type = $item->ozonitemable_type;
            }

            if ($type === Item::class) {
                if ($itemable->supplier_id === $this->service->supplier->id) {
                    return true;
                }
            } else {
                if ($itemable->items->every(fn(Item $item) => $item->supplier_id === $this->service->supplier->id)) {
                    return true;
                }
            }

            return false;

        })->each(function (WbItem|OzonItem $item) {
            $wbItem = $this->service->recountStockWbItem($item);
            $wbItem->save();
        });
    }
}
