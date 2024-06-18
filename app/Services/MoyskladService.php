<?php

namespace App\Services;

use App\HttpClient\MoyskladClient;
use App\Models\Moysklad;
use App\Models\MoyskladWarehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MoyskladService
{
    public MoyskladClient $moyskladClient;
    public Moysklad $moysklad;

    public function __construct(Moysklad $moysklad)
    {
        $this->moysklad = $moysklad;
    }

    public function setClient(): void
    {
        $this->moyskladClient = new MoyskladClient($this->moysklad->api_key);
    }

    public function getWarehouses(): Collection
    {
        return Cache::tags(['moysklad', 'warehouses'])->remember($this->moysklad->id, now()->addHours(8), function () {
            $warehouses = $this->moyskladClient->getWarehouses();

            return $warehouses->map(function (array $warehouse) {
                return collect(['id' => $warehouse['id'], 'name' => $warehouse['name']]);
            });
        });

    }

    public function getWarehouseStocks(MoyskladWarehouse $warehouse)
    {
        return $this->moyskladClient->getWarehouseStocks($warehouse->ms_uuid);
    }
}
