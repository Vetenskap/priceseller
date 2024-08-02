<?php

namespace Modules\Moysklad\Services;

use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;

class MoyskladService
{
    public Moysklad $moysklad;

    public array $mainAttributes = [
        [
            'name' => 'Код',
            'id' => 'code'
        ],
        [
            'name' => 'Внешний код',
            'id' => 'external_code'
        ],
        [
            'name' => 'Артикул',
            'id' => 'article'
        ],
        [
            'name' => 'Наименование',
            'id' => 'name'
        ],
    ];

    /**
     * @param Moysklad $moysklad
     */
    public function __construct(Moysklad $moysklad)
    {
        $this->moysklad = $moysklad;
    }

    public function getAllWarehouses(): Collection
    {
        return Cache::tags(['moysklad', 'warehouses'])->remember($this->moysklad->id, now()->addDay(), function (){
            $client = new MoyskladClient($this->moysklad->api_key);
            return $client->getAllWarehouses();
        });
    }

    public function getAllSuppliers(): Collection
    {
        return Cache::tags(['moysklad', 'suppliers'])->remember($this->moysklad->id, now()->addDay(), function (){
            $client = new MoyskladClient($this->moysklad->api_key);
            return $client->getAllSuppliers();
        });
    }

    public function getAllStocks(MoyskladWarehouseWarehouse $moyskladWarehouse)
    {
        $count = 0;

        $warehouse = $moyskladWarehouse->warehouse;
        $client = new MoyskladClient($this->moysklad->api_key);
        $stocks = $client->getAllStocksFromWarehouse($moyskladWarehouse->moysklad_warehouse_uuid);
        $stocks->each(function (array $stock) use ($warehouse, &$count) {
            if ($item = Item::where('ms_uuid', $stock['assortmentId'])->first()) {

                $count++;

                $warehouse->stocks()->updateOrCreate([
                    'item_id' => $item->id,
                ], [
                    'item_id' => $item->id,
                    'stock' => $stock['stock']
                ]);
            }
        });

        return $count;
    }

    public function getAllAssortmentAttributes(): Collection
    {
        return Cache::tags(['moysklad', 'assortment', 'attributes'])->remember($this->moysklad->id, now()->addDay(), function (){
            $client = new MoyskladClient($this->moysklad->api_key);
            return collect($this->mainAttributes)->merge($client->getAllAssortmentAttributes());
        });
    }

    public function addWarehouseWebhook()
    {

    }
}
