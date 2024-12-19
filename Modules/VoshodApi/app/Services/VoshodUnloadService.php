<?php

namespace Modules\VoshodApi\Services;

use App\Contracts\MarketContract;
use App\Contracts\ReportContract;
use App\Exceptions\ReportCancelled;
use App\Models\Item;
use App\Models\Report;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;
use Modules\VoshodApi\Contracts\VoshodUnloadContract;
use Modules\VoshodApi\HttpClient\Resources\ItemsPageList;
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Models\VoshodApiItemAdditionalAttributeLink;
use Modules\VoshodApi\Models\VoshodApiWarehouse;

class VoshodUnloadService implements VoshodUnloadContract
{
    public VoshodApi $voshodApi;
    public User $user;
    public ReportContract $reportContract;
    public Report $report;

    public function make(VoshodApi $voshodApi, Report $report): void
    {
        $this->voshodApi = $voshodApi;
        $this->user = $voshodApi->user;
        $this->report = $report;
        $this->reportContract = app(ReportContract::class);
    }

    public function getNewPrice(): void
    {
        $this->nullUpdated();
        $this->nullAllStocks();

        $this->reportContract->addLog($this->report, 'Получаем прайс по апи');

        $itemsPageList = new ItemsPageList(
            $this->voshodApi->api_key,
            $this->voshodApi->proxy_ip,
            $this->voshodApi->proxy_port,
            $this->voshodApi->proxy_login,
            $this->voshodApi->proxy_password
        );

        do {

            $this->report = $this->report->fresh();
            if ($this->report->isCancelled()) {
                throw new ReportCancelled('cancelled!');
            }

            try {
                $items = $itemsPageList->fetchNext();
            } catch (RequestException) {
                $itemsPageList->setNext($itemsPageList->getNext() + 1);
                continue;
            }

            $items->each(function (\Modules\VoshodApi\HttpClient\Resources\Item $voshodItem) {

                $price = $voshodItem->getPrice();
                $article = $voshodItem->getMog();
                $brand = $voshodItem->getOemBrand();

                $itemService = new ItemPriceService($article, $this->voshodApi->supplier_id);
                $items = $this->voshodApi->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

                if ($items) {

                    /** @var Item $item */
                    foreach ($items as $item) {

                        foreach ($this->voshodApi->warehouses as $warehouse) {

                            $stock = $voshodItem->{Str::camel('get' . Str::apa($warehouse->name))}();

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

                        $item->price = $price;
                        $item->updated = true;
                        $item->save();

                        $this->voshodApi->itemAdditionalAttributeLinks->each(function (VoshodApiItemAdditionalAttributeLink $link) use ($item, $voshodItem) {

                            $value = $voshodItem->{'get' . Str::apa($link->link)}();

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

        } while ($itemsPageList->hasNext());

        $this->reportContract->addLog($this->report, 'Прайс по апи выгружен');
        $marketContract = app(MarketContract::class);
        $this->reportContract->addLog($this->report, 'Выгружаем новые данные в кабинеты..');
        $marketContract->unload($this->voshodApi->supplier, $this->report);
    }

    public function nullUpdated(): void
    {
        $this->reportContract->addLog($this->report, 'Переводим все товары поставщика в статус "Не обновлён"');
        $this->voshodApi->supplier->items()->update(['updated' => false]);
        $this->reportContract->addLog($this->report, 'Перевели все товары в статус "Не обновлён"');
    }

    public function nullAllStocks(): void
    {
        $this->reportContract->addLog($this->report, 'Обнуляем все остатки поставщика');
        $this->voshodApi->warehouses->each(function (VoshodApiWarehouse $warehouse) {
            $warehouse->supplierWarehouse->stocks()->update(['stock' => 0]);
        });
        $this->reportContract->addLog($this->report, 'Обнулили все остатки поставщика');
    }


}
