<?php

namespace Modules\Moysklad\Services;

use App\Models\Item;
use Illuminate\Support\Collection;
use Mockery\Exception;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\MetaArrays\Position;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookEvent;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookPost;
use Modules\Moysklad\HttpClient\Resources\Webhooks\WebhookStockPost;
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
            case 'product':
                switch ($this->webhook->action) {
                    case 'UPDATE':
                        $this->updateItem();
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
                $this->createOrder();
                break;

        }
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
}
