<?php

namespace Modules\Moysklad\Services;

use App\Models\Bundle;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
use Modules\Moysklad\Models\MoyskladWebhookReport;

class MoyskladWebhookProcessService
{
    public MoyskladService $moyskladService;

    public function __construct(public WebhookPost|WebhookStockPost $apiWebhook, public MoyskladWebhook $webhook, public MoyskladWebhookReport $report)
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
                        $this->recountRetailMarkup();
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

            Log::info('Moysklad webhook change warehouse demand', [
                'event' => $event->toArray()
            ]);

            if ($orderUuid = MoyskladOrderUuid::where('moysklad_id', $this->webhook->moysklad_id)->where('uuid', $demand->getCustomerOrder()->id)->first()) {
                $demand->put($this->webhook->moysklad->api_key, [
                    'store' => [
                        'meta' => [
                            "href" => MoyskladClient::BASEURL . Store::ENDPOINT . '64232c0a-9a30-11ed-0a80-098900246f45',
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

        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            sleep(30);

            $order = $event->getMeta();
            $order->fetch($this->webhook->moysklad->api_key, ['expand' => 'positions']);

            Log::info('Moysklad webhook change warehouse order', [
                'event' => $event->toArray(),
                'order' => $order->toArray()
            ]);

            if ($order->getProject()?->id === 'b4a96157-5f23-11ed-0a80-030b00027f77' && $order->getStore()->id !== '64232c0a-9a30-11ed-0a80-098900246f45' && $order->getAgent()->id === 'e835186b-50f2-11ec-0a80-00190020f74e') {

                /** @var Position $position */
                $position = $order->getPositions()->first();
                $stocksAll = new StocksAll([
                    'filter' => 'product=' . MoyskladClient::BASEURL . Product::ENDPOINT . $position->getAssortment()->id . ';' . 'store=' . MoyskladClient::BASEURL . Store::ENDPOINT . 'c20b3e0e-599d-11ed-0a80-060900042d3e',
                ]);
                $stocksAll->fetchStocks($this->webhook->moysklad->api_key);

                Log::info('Moysklad webhook change warehouse order stocks', [
                    'stocksAll' => $stocksAll->toArray()
                ]);

                $stocksAll->getStocks()->get('rows')->each(function (Collection $stock) use ($order, $stocksAll) {
                    if (intval($stock->get('quantity')) <= 0) {
                        $order->put($this->webhook->moysklad->api_key, [
                            'store' => [
                                'meta' => [
                                    "href" => MoyskladClient::BASEURL . Store::ENDPOINT . '64232c0a-9a30-11ed-0a80-098900246f45',
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

            $product = $event->getMeta();
            $product->fetch($this->webhook->moysklad->api_key);
            $product->getSupplier()->fetch($this->webhook->moysklad->api_key);

            if ($product->isArchived()) return;

            if (!Item::where('ms_uuid', $product->id)->exists()) {

                Log::info('Moysklad webhook create/update item', [
                    'event' => $event->toArray()
                ]);

                $error = $this->moyskladService->createItemFromProduct($product);
                if (!($error instanceof Item)) {
                    $this->report->events()->create([
                        'status' => false,
                        'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                        'message' => 'Товар не создан',
                        'exception' => json_encode(is_string($error) ? [$error] : $error, JSON_UNESCAPED_UNICODE),
                        'itemable_id' => null,
                        'itemable_type' => null,
                    ]);
                    return;
                }

                $this->report->events()->create([
                    'status' => true,
                    'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                    'message' => 'Товар создан',
                    'exception' => json_encode([]),
                    'itemable_id' => $error->getKey(),
                    'itemable_type' => get_class($error),
                ]);
            } else {
                if ($updatedFields->isNotEmpty()) {
                    if ($item = Item::where('ms_uuid', $product->id)->first()) {
                        $product->fetch($this->webhook->moysklad->api_key);

                        Log::info('Moysklad webhook update item', [
                            'updatedFields' => $updatedFields->toArray(),
                            'event' => $event->toArray()
                        ]);

                        $error = $this->moyskladService->updateItemFromProductWithUpdatedFields($product, $item, $updatedFields);
                        if ($error) {
                            $this->report->events()->create([
                                'status' => false,
                                'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                                'message' => 'Товар не обновлен',
                                'exception' => json_encode([$error], JSON_UNESCAPED_UNICODE),
                                'itemable_id' => $item->getKey(),
                                'itemable_type' => get_class($item)
                            ]);
                            return;
                        }

                        $this->report->events()->create([
                            'status' => true,
                            'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                            'message' => 'Товар обновлен ' . $updatedFields->toJson(JSON_UNESCAPED_UNICODE),
                            'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                            'itemable_id' => $item->getKey(),
                            'itemable_type' => get_class($item),
                        ]);
                    } else {
                        $this->report->events()->create([
                            'status' => false,
                            'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                            'message' => 'Товар не найден',
                            'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                            'itemable_id' => null,
                            'itemable_type' => null,
                        ]);
                    }

                } else {
                    $this->report->events()->create([
                        'status' => false,
                        'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                        'message' => 'Нет нужных полей для обновления товара',
                        'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                        'itemable_id' => null,
                        'itemable_type' => null,
                    ]);
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

            Log::info('Moysklad webhook create item', [
                'event' => $event->toArray()
            ]);

            $error = $this->moyskladService->createItemFromProduct($product);
            if (!($error instanceof Item)) {
                $this->report->events()->create([
                    'status' => false,
                    'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                    'message' => 'Товар не создан',
                    'exception' => json_encode(is_string($error) ? [$error] : $error, JSON_UNESCAPED_UNICODE),
                    'itemable_id' => null,
                    'itemable_type' => null,
                ]);
                return;
            }

            $this->report->events()->create([
                'status' => true,
                'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                'message' => 'Товар создан',
                'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                'itemable_id' => $error->getKey(),
                'itemable_type' => get_class($error)
            ]);
        });

    }

    private function createOrder(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $order = $event->getMeta();
            $order->fetchPositions($this->webhook->moysklad->api_key);

            $order->getPositions()->each(function (Position $position) use ($event) {

                $item = $this->webhook->moysklad->user->items()->where('ms_uuid', $position->getAssortment()->id)->first();
                if ($item) {
                    $this->webhook->moysklad->orders()->create([
                        'item_id' => $item->id,
                        'orders' => $position->getQuantity()
                    ]);
                    $this->report->events()->create([
                        'status' => true,
                        'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                        'message' => 'Для товара установлен заказ',
                        'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                        'itemable_id' => $item->getKey(),
                        'itemable_type' => get_class($item)
                    ]);
                } else {
                    $this->report->events()->create([
                        'status' => false,
                        'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                        'message' => 'Товар не найден',
                        'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                        'itemable_id' => null,
                        'itemable_type' => null
                    ]);
                }
            });

        });
    }

    private function recountRetailMarkup(): void
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $recountRetailMarkups = collect();

            Log::info('Moysklad recountRetailMarkups updatedFields', [
                'value' => $event->getUpdatedFields()
            ]);
            $updatedFields = $event->getUpdatedFields()->contains('buyPrice');
            Log::info('Moysklad recountRetailMarkups updatedFields result', [
                'value' => $updatedFields
            ]);
            if (!$updatedFields) {
                $this->webhook->moysklad->recountRetailMarkups->each(function (MoyskladRecountRetailMarkup $recountRetailMarkup) use ($event, &$recountRetailMarkups) {
                    Log::info('Moysklad recountRetailMarkups linkName', [
                        'value' => $recountRetailMarkup->link_name
                    ]);
                    if ($event->getUpdatedFields()->contains($recountRetailMarkup->link_name)) {
                        $recountRetailMarkups->push($recountRetailMarkup);
                    }
                });
            } else {
                $recountRetailMarkups = $this->webhook->moysklad->recountRetailMarkups;
                Log::info('Moysklad recountRetailMarkups else', $recountRetailMarkups->toArray());
            }

            $recountRetailMarkups = $recountRetailMarkups->filter(fn(MoyskladRecountRetailMarkup $recountRetailMarkup) => $recountRetailMarkup->enabled);

            Log::info('Moysklad recountRetailMarkups', $recountRetailMarkups->toArray());

            if ($recountRetailMarkups->isNotEmpty()) {

                $product = $event->getMeta();
                $product->fetch($this->webhook->moysklad->api_key);

                $recountRetailMarkups->each(function (MoyskladRecountRetailMarkup $recountRetailMarkup) use ($product, $event) {

                    $retail_markup_percent = (int)MoyskladService::getValueFromAttributesAndProduct(
                        $recountRetailMarkup->link_type,
                        $recountRetailMarkup->link,
                        $product,
                    );

                    Log::info('value attribute percent', [
                        'value' => $retail_markup_percent
                    ]);

                    Log::info('sale prices', [
                        'prices type ids' => $product->getSalePrices()->map(fn (SalePrice $salePrice) => $salePrice->getPriceType()->id),
                        'price type uuid' => $recountRetailMarkup->price_type_uuid
                    ]);

                    /** @var SalePrice $salePrice */
                    $salePrice = $product->getSalePrices()->firstWhere(fn(SalePrice $salePrice) => $salePrice->getPriceType()->id === $recountRetailMarkup->price_type_uuid);

                    Log::info('sale price', [
                        'sale price id' => $salePrice->getPriceType()->id
                    ]);

                    if ($salePrice) {
                        $salePrice->setValue($product->getBuyPrice()->getValue() * ($retail_markup_percent / 100));
                        $product->update($this->webhook->moysklad->api_key, ['salePrices' => [$salePrice]]);
                        $this->report->events()->create([
                            'status' => true,
                            'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                            'message' => 'Для товара перерасчитана цена',
                            'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                        ]);
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

                $error = $this->moyskladService->updateBundle($bundle, $userBundle);
                if ($error) {
                    $this->report->events()->create([
                        'status' => false,
                        'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                        'message' => 'Комлект не обновлен',
                        'exception' => json_encode([$error], JSON_UNESCAPED_UNICODE),
                        'itemable_id' => $userBundle->getKey(),
                        'itemable_type' => get_class($userBundle),
                    ]);
                    return;
                }

                $this->report->events()->create([
                    'status' => true,
                    'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                    'message' => 'Комлект обновлен',
                    'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                    'itemable_id' => $userBundle->getKey(),
                    'itemable_type' => get_class($userBundle),
                ]);
            }

        });
    }

    private function createBundle()
    {
        $this->apiWebhook->getEvents()->each(function (WebhookEvent $event) {

            $bundle = $event->getMeta();
            $bundle->fetch($this->webhook->moysklad->api_key);

            $error = $this->moyskladService->createBundle($bundle);
            if (is_string($error)) {
                $this->report->events()->create([
                    'status' => false,
                    'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                    'message' => 'Комлект не создан',
                    'exception' => json_encode([$error], JSON_UNESCAPED_UNICODE),
                    'itemable_id' => null,
                    'itemable_type' => null,
                ]);
                return;
            }

            $this->report->events()->create([
                'status' => true,
                'event' => json_encode($event->toArray(), JSON_UNESCAPED_UNICODE),
                'message' => 'Комлект создан',
                'exception' => json_encode([], JSON_UNESCAPED_UNICODE),
                'itemable_id' => $error->getKey(),
                'itemable_type' => get_class($error)
            ]);
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
