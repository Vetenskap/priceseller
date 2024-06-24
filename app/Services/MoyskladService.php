<?php

namespace App\Services;

use App\HttpClient\MoyskladClient;
use App\Imports\ItemsMsImport;
use App\Models\Moysklad;
use App\Models\MoyskladWarehouse;
use App\Services\Item\ItemService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MoyskladService
{
    public MoyskladClient $moyskladClient;
    public Moysklad $moysklad;

    const PATH = 'users/moysklad/';

    public array $attributes = [
        [
            'name' => 'Наименование',
            'id' => 'name'
        ],
        [
            'name' => 'Описание',
            'id' => 'description'
        ],
        [
            'name' => 'Код',
            'id' => 'code'
        ],
        [
            'name' => 'Внешний код',
            'id' => 'externalCode'
        ],
        [
            'name' => 'Архивный',
            'id' => 'archived'
        ],
        [
            'name' => 'Артикул',
            'id' => 'article'
        ],
    ];

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

    public function getItemInfo(): Collection
    {
        return Cache::tags(['moysklad', 'attributes'])->remember($this->moysklad->id, now()->addHours(8), function () {
            $attributes = $this->moyskladClient->getProductAttributes();

            /** @var Collection $attributes */
            $attributes = $attributes->map(function (array $attribute) {
                return ['name' => $attribute['name'], 'id' => $attribute['id']];
            });

            return $attributes->merge(collect($this->attributes));

        });
    }

    public function getSuppliers(): Collection
    {
        return Cache::tags(['moysklad', 'suppliers'])->remember($this->moysklad->id, now()->addHours(8), function () {
            $suppliers = $this->moyskladClient->getSuppliers();

            /** @var Collection $suppliers */
            $suppliers = $suppliers->map(function (array $supplier) {
                return ['name' => $supplier['name'], 'id' => $supplier['id']];
            });

            return $suppliers;
        });
    }

    public function importItemsApi(Collection $attributes): Collection
    {
        $correct = 0;
        $error = 0;
        $updated = 0;

        $offset = 0;
        $limit = 1000;

        do {

            /** @var Collection $result */
            $result = Cache::tags(['moysklad', 'assortment'])->remember($this->moysklad->id . $offset, now()->addHours(8), function () use ($offset, $limit) {
                return $this->moyskladClient->getAssortment($offset, $limit);
            });

            $meta = collect($result->get('meta'));

            $rows = collect($result->get('rows'));

            $rows->each(function (array $row) use ($attributes, &$correct, &$error, &$updated) {

                $row = collect($row);

                $extraFields = collect($row->get('attributes'));

                $uuid = $row->get('id');
                $code = $row->get($attributes->get('code')) ?? collect($extraFields->firstWhere('id', $attributes->get('code')))?->get('value');
                $name = $row->get($attributes->get('name')) ?? collect($extraFields->firstWhere('id', $attributes->get('name')))?->get('value');
                $article = $row->get($attributes->get('article')) ?? collect($extraFields->firstWhere('id', $attributes->get('article')))?->get('value');
                $multiplicity = $row->get($attributes->get('multiplicity')) ?? collect($extraFields->firstWhere('id', $attributes->get('multiplicity')))?->get('value');
                $brand = $row->get($attributes->get('brand')) ?? collect($extraFields->firstWhere('id', $attributes->get('brand')))?->get('value');
                $supplier_id = $row->get('supplier') ? str_replace('https://api.moysklad.ru/api/remap/1.2/entity/counterparty/', '', $row->get('supplier')['meta']['href']) : null;

                $response = ItemService::store([
                    'ms_uuid' => $uuid,
                    'code' => $code,
                    'name' => $name,
                    'article' => $article,
                    'multiplicity' => $multiplicity,
                    'brand' => $brand,
                    'supplier_id' => $supplier_id
                ], $this->moysklad->user_id);

                if ($response['status'] === 'success') $correct++;
                else if ($response['status'] === 'updated') $updated++;
                else {

                    $error++;

                    foreach ($response['errors'] as $attribute => $message) {
                        ItemsMoyskladImportReportService::addBadItem(
                            $this->moysklad,
                            0,
                            $attribute,
                            $message,
                            $row->all()
                        );
                    }
                }
            });

            ItemsMoyskladImportReportService::flush($this->moysklad, $correct, $error, $updated);

            $offset += $limit;
        } while ($rows->isNotEmpty());

        return collect(['correct' => $correct, 'error' => $error, 'updated' => $updated]);
    }

    public function importItemsExcel(string $uuid, string $ext, Collection $attributes)
    {
        $import = new ItemsMsImport($this->moysklad->user_id, $attributes);

        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');

        return collect(['correct' => $import->correct, 'error' => $import->error, 'updated' => $import->updated]);
    }
}
