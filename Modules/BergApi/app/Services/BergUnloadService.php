<?php

namespace Modules\BergApi\Services;

use App\Contracts\MarketContract;
use App\Contracts\ReportContract;
use App\Exceptions\ReportCancelled;
use App\Models\Item;
use App\Models\Report;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\BergApi\HttpClient\BergApiClient;
use Modules\BergApi\HttpClient\Resources\Offer;
use Modules\BergApi\HttpClient\Resources\Resource;
use Modules\BergApi\Models\BergApi;
use Modules\BergApi\Models\BergApiItemAdditionalAttributeLink;
use Modules\BergApi\Models\BergApiWarehouse;

class BergUnloadService
{
    public BergApi $bergApi;
    public User $user;
    public ReportContract $reportContract;
    public Report $report;

    public function make(BergApi $bergApi, Report $report): void
    {
        $this->bergApi = $bergApi;
        $this->user = $bergApi->user;
        $this->report = $report;
        $this->reportContract = app(ReportContract::class);
    }

    public function getNewPrice(): void
    {
        $this->nullUpdated();
        $this->nullAllStocks();

        $this->reportContract->addLog($this->report, 'Получаем прайс по апи');

        $this->bergApi->supplier->items()->chunk(50, function (Collection $items) {

            $this->report = $this->report->fresh();
            if ($this->report->isCancelled()) throw new ReportCancelled('cancelled!');

            $data = $items->map(function (Item $item) {
                return ['resource_article' => $item->article, 'brand_name' => $item->brand];
            });

            $client = new BergApiClient();
            $result = $client->get(Resource::ENDPOINT, [
                'key' => $this->bergApi->api_key,
                'items' => $data->toArray()
            ]);

            $resources = $result->toCollectionSpread()->get('resources')->map(function (Collection $resource) {
                return new Resource($resource);
            });

            /** @var Resource $resource */
            foreach ($resources as $resource) {

                $itemService = new ItemPriceService($resource->getArticle(), $this->bergApi->supplier_id);
                $items = $this->bergApi->supplier->use_brand ? $itemService->withBrand($resource->getBrandName())->find() : $itemService->find();
                $price = $resource->getOffers()->firstWhere(fn (Offer $offer) => in_array($offer->getWarehouseName(), $this->bergApi->warehouses->pluck('warehouse_name')->toArray()))?->getPrice();

                if ($items && $price) {

                    /** @var Item $item */
                    foreach ($items as $item) {

                        $this->bergApi->warehouses()->each(function (BergApiWarehouse $warehouse) use ($resource, $item) {

                            /** @var Offer $offer */
                            $offer = $resource->getOffers()->firstWhere(fn (Offer $offer) => $offer->getWarehouseName() == $warehouse->warehouse_name);

                            if ($offer) {
                                $stock = $offer->getQuantity();

                                if ($stock >= 0) {
                                    $item->supplierWarehouseStocks()->updateOrCreate([
                                        'supplier_warehouse_id' => $warehouse->supplier_warehouse_id,
                                        'item_id' => $item->id
                                    ], [
                                        'supplier_warehouse_id' => $warehouse->supplier_warehouse_id,
                                        'stock' => $stock
                                    ]);
                                }
                            }
                        });

                        $item->price = $price;
                        $item->updated = true;
                        $item->save();

                        $this->bergApi->itemAdditionalAttributeLinks->each(function (BergApiItemAdditionalAttributeLink $link) use ($item, $resource) {

                            $value = $resource->{'get' . Str::apa($link->link)}();

                            if (!is_null($value)) {
                                $item->attributesValues()->updateOrCreate([
                                    'item_attribute_id' => $link->item_attribute_id
                                ], [
                                    'item_attribute_id' => $link->item_attribute_id,
                                    'value' => $value,
                                ]);
                            }
                        });

                    }
                }

            }

        });

        $this->reportContract->addLog($this->report, 'Прайс по апи выгружен');
        $marketContract = app(MarketContract::class);
        $this->reportContract->addLog($this->report, 'Выгружаем новые данные в кабинеты..');
        $marketContract->unload($this->bergApi->supplier, $this->report);

    }

    public function nullUpdated(): void
    {
        $this->reportContract->addLog($this->report, 'Переводим все товары поставщика в статус "Не обновлён"');
        $this->bergApi->supplier->items()->update(['updated' => false]);
        $this->reportContract->addLog($this->report, 'Перевели все товары в статус "Не обновлён"');
    }

    public function nullAllStocks(): void
    {
        $this->reportContract->addLog($this->report, 'Обнуляем все остатки поставщика');
        $this->bergApi->warehouses->each(function (BergApiWarehouse $warehouse) {
            $warehouse->supplierWarehouse->stocks()->update(['stock' => 0]);
        });
        $this->reportContract->addLog($this->report, 'Обнулили все остатки поставщика');
    }
}
