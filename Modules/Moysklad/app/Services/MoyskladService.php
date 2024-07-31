<?php

namespace Modules\Moysklad\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\Models\Moysklad;

class MoyskladService
{
    public Moysklad $moysklad;

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

    public function getAllStocks(string $warehouseId): Collection
    {
        $client = new MoyskladClient($this->moysklad->api_key);
        return $client->getAllStocksFromWarehouse($warehouseId);
    }
}
