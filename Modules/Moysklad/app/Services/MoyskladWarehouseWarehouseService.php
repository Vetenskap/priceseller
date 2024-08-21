<?php

namespace Modules\Moysklad\Services;

use App\Models\Item;
use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Reports\StocksAll;
use Modules\Moysklad\HttpClient\Resources\Reports\StocksByStore;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;

class MoyskladWarehouseWarehouseService
{
    public Moysklad $moysklad;
    public MoyskladWarehouseWarehouse $moyskladWarehouse;

    public function __construct(MoyskladWarehouseWarehouse $moyskladWarehouse, Moysklad $moysklad)
    {
        $this->moyskladWarehouse = $moyskladWarehouse;
        $this->moysklad = $moysklad;
    }

    public function updateAllStocks(): int
    {
        $this->moyskladWarehouse->warehouse->stocks()->delete();

        $count = 0;

        $warehouse = $this->moyskladWarehouse->warehouse;

        $stocksAll = new StocksByStore();
        $stocksAll->fetchStocksByStore($this->moysklad->api_key, $this->moyskladWarehouse->moysklad_warehouse_uuid);

        $stocksAll->getStocks()->each(function (Collection $report) use ($warehouse, &$count) {
            if ($item = Item::where('ms_uuid', $report->get('assortmentId'))->first()) {

                $count++;

                $warehouse->stocks()->updateOrCreate([
                    'item_id' => $item->id,
                ], [
                    'item_id' => $item->id,
                    'stock' => $report->get('stock')
                ]);
            }
        });

        return $count;
    }
}
