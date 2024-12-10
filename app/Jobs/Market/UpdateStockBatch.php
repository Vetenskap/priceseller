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
    public function __construct(public WbItemPriceService|OzonItemPriceService $service, public int $offset)
    {
        $this->queue = 'market-unload';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $this->service->market
            ->items()
            ->with('itemable')
            ->limit(10000)
            ->offset($this->offset)
            ->get()
            ->filter(function (WbItem|OzonItem $item) {

                if ($item instanceof WbItem) {
                    $itemable = $item->itemable;
                    $type = $item->wbitemable_type;
                } else {
                    $itemable = $item->itemable;
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
                if ($item instanceof WbItem) {
                    $item = $this->service->recountStockWbItem($item);
                } else {
                    $item = $this->service->recountStockOzonItem($item);
                }
                $item->save();
            });
    }
}
