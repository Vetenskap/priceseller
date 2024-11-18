<?php

namespace Modules\Moysklad\Services;

use App\Models\Bundle;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\MetaArrays\Position;
use Modules\Moysklad\HttpClient\Resources\Entities\Demand;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\HttpClient\Resources\Entities\Store;
use Modules\Moysklad\HttpClient\Resources\Objects\SalePrice;
use Modules\Moysklad\HttpClient\Resources\Reports\StocksAll;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookEvent;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookPost;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookStockPost;
use Modules\Moysklad\Models\MoyskladOrderUuid;
use Modules\Moysklad\Models\MoyskladRecountRetailMarkup;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Models\MoyskladWebhook;

class MoyskladWebhookProcessService
{
    public MoyskladService $moyskladService;

    public function __construct(public WebhookPost|WebhookStockPost $apiWebhook, public MoyskladWebhook $webhook)
    {
        $this->moyskladService = new MoyskladService($this->webhook->moysklad);
    }

    public function process(): void
    {

        switch ($this->webhook->type) {
            case 'warehouses':
                $this->updateWarehousesStocks();
                break;
            case 'demand':
                $this->processChangeWarehouseDemand();
                break;
            case 'bundle':
                switch ($this->webhook->action) {
                    case 'UPDATE':
                        $this->updateBundle();
                        break;
                    case 'CREATE':
                        $this->createBundle();
                        break;
                    case 'DELETE':
                        $this->deleteBundle();
                        break;
                }
                break;
            case 'product':
                switch ($this->webhook->action) {
                    case 'UPDATE':
                        $this->updateItem();
                        if ($this->webhook->moysklad->enabled_recount_retail_markup) {
                            $this->recountRetailMarkup();
                        }
                        break;
                    case 'CREATE':
                        $this->createItem();
                        break;
                    case 'DELETE':
                        $this->deleteItem();
                        break;
                }
                break;
            case 'customerorder':
                $this->processChangeWarehouseOrder();
                $this->createOrder();
                break;

        }
    }

    private function processChangeWarehouseDemand()
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            /** @var Demand $demand */
            $demand = $event->getMeta();
            $demand->fetch($this->webhook->moysklad->api_key);
            if ($orderUuid = MoyskladOrderUuid::where('moysklad_id', $this->webhook->moysklad_id)->where('uuid', $demand->getCustomerOrder()->id)->first()) {
                $demand->put($this->webhook->moysklad->api_key, [
                    'store' => [
                        'meta' => [
                            "href" => MoyskladClient::BASEURL . Store::ENDPOINT . 'c20b3e0e-599d-11ed-0a80-060900042d3e',
                            "metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/store/metadata",
                            "type" => "store",
                            "mediaType" => "application/json",
                        ]
                    ]
                ]);
                $orderUuid->delete();
            }

        });
    }

    private function processChangeWarehouseOrder()
    {
        logger('Задержка');
        sleep(10);
        logger('Начали');

        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $order = $event->getMeta();
            $order->fetch($this->webhook->moysklad->api_key, ['expand' => 'positions']);

            if ($order->getProject()?->id === 'b4a96157-5f23-11ed-0a80-030b00027f77' && $order->getStore()->id !== 'c20b3e0e-599d-11ed-0a80-060900042d3e' && $order->getAgent()->id === 'e835186b-50f2-11ec-0a80-00190020f74e') {

                /** @var Position $position */
                $position = $order->getPositions()->first();
                $stocksAll = new StocksAll([
                    'filter' => 'product=' . MoyskladClient::BASEURL . Product::ENDPOINT . $position->getAssortment()->id . ';' . 'store=' . MoyskladClient::BASEURL . Store::ENDPOINT . '64232c0a-9a30-11ed-0a80-098900246f45',
                ]);
                $stocksAll->fetchStocks($this->webhook->moysklad->api_key);

                logger('filter=product=' . MoyskladClient::BASEURL . Product::ENDPOINT . $position->getAssortment()->id . ';' . 'store=' . MoyskladClient::BASEURL . Store::ENDPOINT . '64232c0a-9a30-11ed-0a80-098900246f45');
                logger('stocks');
                logger($stocksAll->getStocks());

                $stocksAll->getStocks()->get('rows')->each(function (Collection $stock) use ($order, $stocksAll) {
                    logger('stock');
                    logger($stock);
                    if (intval($stock->get('quantity')) < 0) {
                        $order->put($this->webhook->moysklad->api_key, [
                            'store' => [
                                'meta' => [
                                    "href" => MoyskladClient::BASEURL . Store::ENDPOINT . 'c20b3e0e-599d-11ed-0a80-060900042d3e',
                                    "metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/store/metadata",
                                    "type" => "store",
                                    "mediaType" => "application/json",
                                ]
                            ]
                        ]);

                        MoyskladOrderUuid::create([
                            'moysklad_id' => $this->webhook->moysklad_id,
                            'uuid' => $order->id,
                        ]);

                        return false;
                    }
                });

            }
        });
    }

    private function updateWarehousesStocks(): void
    {
        $stocksByStore = $this->apiWebhook->getStocksByStore();
        $stocksByStore->fetchStocks($this->webhook->moysklad->api_key);
        $stocksByStore->getStocks()->each(function (Collection $stock) {

            if ($stock->get('stock') < 0) return;

            if ($item = Item::where('ms_uuid', $stock->get('assortmentId'))->first()) {

                if ($moyskaldWarehouse = MoyskladWarehouseWarehouse::where('moysklad_warehouse_uuid', $stock->get('storeId'))->first()) {
                    $moyskaldWarehouse->warehouse->stocks()->updateOrCreate([
                        'item_id' => $item->id,
                    ], [
                        'item_id' => $item->id,
                        'stock' => $stock->get('stock')
                    ]);
                }
            }
        });
    }

    private function updateItem(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $updatedFieldsMain = $event->getUpdatedFields()->intersect($this->webhook->moysklad->itemMainAttributeLinks->pluck('link_name'));
            $updatedFieldsAdditional = $event->getUpdatedFields()->intersect($this->webhook->moysklad->itemAdditionalAttributeLinks->pluck('link_name'));
            $updatedFields = $updatedFieldsMain->merge($updatedFieldsAdditional);

            if ($updatedFields) {

                $product = $event->getMeta();
                $product->fetch($this->webhook->moysklad->api_key);

                if ($item = Item::where('ms_uuid', $product->id)->first()) {

                    $this->moyskladService->updateItemFromProductWithUpdatedFields($product, $item, $updatedFields);

                }

            }

        });
    }

    private function deleteItem(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $product = $event->getMeta();

            $this->webhook->moysklad->user->items()->where('ms_uuid', $product->id)->delete();

        });
    }

    private function createItem(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $product = $event->getMeta();
            $product->fetch($this->webhook->moysklad->api_key);

            $this->moyskladService->createItemFromProduct($product);
        });

    }

    private function createOrder(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $order = $event->getMeta();
            $order->fetchPositions($this->webhook->moysklad->api_key);

            $order->getPositions()->each(function (Position $position) {

                $item = $this->webhook->moysklad->user->items()->where('ms_uuid', $position->getAssortment()->id)->first();
                if ($item) {
                    $this->webhook->moysklad->orders()->create([
                        'item_id' => $item->id,
                        'orders' => $position->getQuantity()
                    ]);
                }
            });

        });
    }

    private function recountRetailMarkup(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $recountRetailMarkups = collect();

            $updatedFields = $event->getUpdatedFields()->get('buyPrice');
            if (!$updatedFields) {
                $this->webhook->moysklad->recountRetailMarkups->each(function (MoyskladRecountRetailMarkup $recountRetailMarkup) use ($event, &$recountRetailMarkups) {
                    if ($event->getUpdatedFields()->get($recountRetailMarkup->link_name)) {
                        $recountRetailMarkups->push($recountRetailMarkup);
                    }
                });
            } else {
                $recountRetailMarkups = $this->webhook->moysklad->recountRetailMarkups;
            }

            if ($recountRetailMarkups->isNotEmpty()) {

                $product = $event->getMeta();
                $product->fetch($this->webhook->moysklad->api_key);

                $recountRetailMarkups->each(function (MoyskladRecountRetailMarkup $recountRetailMarkup) use ($product) {

                    $retail_markup_percent = MoyskladService::getValueFromAttributesAndProduct(
                        $recountRetailMarkup->link_type,
                        $recountRetailMarkup->link,
                        $product,
                    );

                    if (is_int($retail_markup_percent)) {

                        $salePrice = $product->getSalePrices()->firstWhere(fn(SalePrice $salePrice) => $salePrice->getPriceType()->id === $recountRetailMarkup->price_type_uuid);

                        if ($salePrice) {
                            $salePrice->setValue($product->getBuyPrice()->getValue() * ($retail_markup_percent / 100));
                            $product->update($this->webhook->moysklad->api_key, ['salePrices' => [$salePrice]]);
                        }

                    }
                });

            }

        });
    }

    private function updateBundle(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $bundle = $event->getMeta();
            $bundle->fetch($this->webhook->moysklad->api_key);

            if ($userBundle = Bundle::where('ms_uuid', $bundle->id)->first()) {

                $this->moyskladService->updateBundle($bundle, $userBundle);

            }

        });
    }

    private function createBundle()
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $bundle = $event->getMeta();
            $bundle->fetch($this->webhook->moysklad->api_key);

            $this->moyskladService->createBundle($bundle);
        });
    }

    private function deleteBundle()
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $bundle = $event->getMeta();

            $this->webhook->moysklad->user->bundles()->where('ms_uuid', $bundle->id)->delete();

        });
    }
}
