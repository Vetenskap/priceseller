<?php

namespace Modules\SamsonApi\Services;

use App\Contracts\MarketContract;
use App\Contracts\ReportContract;
use App\Exceptions\ReportCancelled;
use App\Models\Item;
use App\Models\Report;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use Illuminate\Support\Str;
use Modules\SamsonApi\Contracts\SamsonUnloadContract;
use Modules\SamsonApi\HttpClient\Resources\Sku;
use Modules\SamsonApi\HttpClient\Resources\SkuList;
use Modules\SamsonApi\Models\SamsonApi;
use Modules\SamsonApi\Models\SamsonApiItemAdditionalAttributeLink;

class SamsonUnloadService implements SamsonUnloadContract
{
    public SamsonApi $samsonApi;
    public User $user;
    public ReportContract $reportContract;
    public Report $report;

    public function make(SamsonApi $samsonApi, Report $report): void
    {
        $this->samsonApi = $samsonApi;
        $this->user = $samsonApi->user;
        $this->report = $report;
        $this->reportContract = app(ReportContract::class);
    }

    public function getNewPrice(): void
    {
        $this->nullUpdated();
        $this->nullAllStocks();

        $this->reportContract->addLog($this->report, 'Получаем прайс по апи');

        $skuList = new SkuList($this->samsonApi->api_key);

        do {
            $this->report = $this->report->fresh();
            if ($this->report->isCancelled()) throw new ReportCancelled('cancelled!');

            $items = $skuList->fetchNext();

            $items->each(function (Sku $samsonItem) {

                $price = $samsonItem->getContract();
                $article = $samsonItem->getSku();
                $brand = $samsonItem->getBrand();
                $count = $samsonItem->getIdp();

                $itemService = new ItemPriceService($article, $this->samsonApi->supplier_id);
                $items = $this->samsonApi->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

                if ($items) {

                    /** @var Item $item */
                    foreach ($items as $item) {

                        if ($count >= 0) {
                            $item->supplierWarehouseStocks()->updateOrCreate([
                                'supplier_warehouse_id' => $this->samsonApi->supplier_warehouse_id,
                                'item_id' => $item->id
                            ], [
                                'supplier_warehouse_id' => $this->samsonApi->supplier_warehouse_id,
                                'stock' => $count
                            ]);
                        }

                        $item->price = $price;
                        $item->updated = true;
                        $item->save();

                        $this->samsonApi->itemAdditionalAttributeLinks->each(function (SamsonApiItemAdditionalAttributeLink $link) use ($item, $samsonItem) {

                            $value = $samsonItem->{'get' . Str::apa($link->link)}();

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

            });

        } while ($skuList->hasNext());

        $this->reportContract->addLog($this->report, 'Прайс по апи выгружен');
        $marketContract = app(MarketContract::class);
        $this->reportContract->addLog($this->report, 'Выгружаем новые данные в кабинеты..');
        $marketContract->unload($this->samsonApi->supplier, $this->report);
    }

    public function nullUpdated(): void
    {
        $this->reportContract->addLog($this->report, 'Переводим все товары поставщика в статус "Не обновлён"');
        $this->samsonApi->supplier->items()->update(['updated' => false]);
        $this->reportContract->addLog($this->report, 'Перевели все товары в статус "Не обновлён"');
    }

    public function nullAllStocks(): void
    {
        $this->reportContract->addLog($this->report, 'Обнуляем все остатки поставщика');
        $this->samsonApi->supplierWarehouse->stocks()->update(['stock' => 0]);
        $this->reportContract->addLog($this->report, 'Обнулили все остатки поставщика');
    }
}
