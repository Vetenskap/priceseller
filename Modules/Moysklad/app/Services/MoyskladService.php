<?php

namespace Modules\Moysklad\Services;

use App\Models\Item;
use App\Services\Item\ItemService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\MoyskladClientActions;
use Modules\Moysklad\HttpClient\Resources\Entities\Counterparty;
use Modules\Moysklad\HttpClient\Resources\Entities\EntityList;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Metadata\Attribute;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\HttpClient\Resources\Entities\Store;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladItemAdditionalAttributeLink;
use Modules\Moysklad\Models\MoyskladItemMainAttributeLink;
use Modules\Moysklad\Models\MoyskladWarehouseWarehouse;
use Modules\Moysklad\Models\MoyskladWebhook;

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
        return Cache::tags(['moysklad', 'warehouses'])->remember($this->moysklad->id, now()->addDay(), function () {

            $entityList = new EntityList(Store::class, $this->moysklad->api_key);

            $allWarehouses = collect();

            do {

                $warehouses = $entityList->getNext()->map(function (Store $warehouse) {
                    return ['id' => $warehouse->id, 'name' => $warehouse->getName()];
                });

                $allWarehouses = $allWarehouses->merge($warehouses);

            } while ($entityList->hasNext());

            return $allWarehouses;

        });
    }

    public function getAllSuppliers(): Collection
    {
        return Cache::tags(['moysklad', 'suppliers'])->remember($this->moysklad->id, now()->addDay(), function () {
            $entityList = new EntityList(Counterparty::class, $this->moysklad->api_key);

            $allSuppliers = collect();

            do {

                $suppliers = $entityList->getNext()->map(function (Counterparty $supplier) {
                    return ['id' => $supplier->id, 'name' => $supplier->getName()];
                });

                $allSuppliers = $allSuppliers->merge($suppliers);

            } while ($entityList->hasNext());

            return $allSuppliers;
        });
    }

    public function getAllAssortmentAttributes()
    {
        return Cache::tags(['moysklad', 'assortment', 'attributes'])->remember($this->moysklad->id, now()->addDay(), function () {

            $entityList = new EntityList(Attribute::class, $this->moysklad->api_key);

            $allAttributes = collect(Product::FIELDS);

            do {

                $attributes = $entityList->getNext()->map(function (Attribute $attribute) {
                    return ['name' => $attribute->getId(), 'label' => $attribute->getName(), 'type' => 'metadata'];
                });

                $allAttributes = $allAttributes->merge($attributes);

            } while ($entityList->hasNext());

            return $allAttributes;

        });
    }

    public function importApiItems(): void
    {
        $offset = Cache::tags(['moysklad', 'product', 'offset'])->get($this->moysklad->id, 0);

        $entityList = new EntityList(Product::class, $this->moysklad->api_key, offset: $offset);

        $itemService = new ItemService($this->moysklad->user);

        do {

            $products = $entityList->getNext();

            $dirtyItems = ['items' => []];

            $products->each(function (Product $product) use (&$dirtyItems) {

                if ($item = $this->moysklad->user->items()->where('ms_uuid', $product->id)
                    ->orWhere('code', $this->getValueFromAttributesAndProduct($this->moysklad->itemMainAttributeLinks->where('attribute_name', 'code')->first(), $product))->first()) {
                    $this->updateItemFromProduct($product, $item);
                } else {
                    $dirtyItem = $this->createItemFromProduct($product);

                    if ($dirtyItem) {
                        $dirtyItems['items'][] = $this->createItemFromProduct($product);
                    }
                }

            });

            $itemService->createFromMs($dirtyItems);

            Cache::tags(['moysklad', 'product', 'offset'])->set($this->moysklad->id, $entityList->getOffset(), now()->addDay());

        } while ($entityList->hasNext());
    }

    public function createItemFromProduct(Product $product): array
    {
        $data = [];

        $supplier = $this->moysklad->suppliers->where('moysklad_supplier_uuid', $product->getSupplier()?->id)->first()?->supplier;

        if ($supplier) {
            $data = [
                'attributes' => []
            ];

            $data['supplier_id'] = $supplier->id;
            $data['ms_uuid'] = $product->id;

            foreach ($this->moysklad->itemMainAttributeLinks as $itemMainAttributeLink) {
                $value = $this->getValueFromAttributesAndProduct($itemMainAttributeLink, $product);

                $data[$itemMainAttributeLink->attribute_name] = $value;
            }

            foreach ($this->moysklad->itemAdditionalAttributeLinks as $itemAdditionalAttributeLink) {

                $value = $this->getValueFromAttributesAndProduct($itemAdditionalAttributeLink, $product);

                if ($value) {

                    $data['attributes'][] = [
                        'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                        'value' => $value
                    ];
                }
            }
        }

        return $data;
    }

    public function updateItemFromProductWithUpdatedFields(Product $product, Item $item, Collection $updatedFields): void
    {
        $updatedFields->each(function (string $updatedField) use ($product, $item) {

            if ($itemMainAttributeLink = $this->moysklad->itemMainAttributeLinks->where('link_name', $updatedField)->first()) {
                $value = $this->getValueFromAttributesAndProduct($itemMainAttributeLink, $product);

                $item->{$itemMainAttributeLink->attribute_name} = $value;

                try {
                    $item->save();
                } catch (\Throwable $e) {
                    report($e);
                    return;
                }
            }

            if ($itemAdditionalAttributeLink = $this->moysklad->itemAdditionalAttributeLinks->where('link_name', $updatedField)->first()) {

                $value = $this->getValueFromAttributesAndProduct($itemAdditionalAttributeLink, $product);

                $item->attributesValues()->updateOrCreate([
                    'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                ], [
                    'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                    'value' => $value
                ]);
            }

        });
    }

    public function updateItemFromProduct(Product $product, Item $item): void
    {
        foreach ($this->moysklad->itemMainAttributeLinks as $itemMainAttributeLink) {

            $value = $this->getValueFromAttributesAndProduct($itemMainAttributeLink, $product);

            $item->{$itemMainAttributeLink->attribute_name} = $value;

            try {
                $item->save();
            } catch (\Throwable $e) {
                report($e);
                return;
            }
        }

        foreach ($this->moysklad->itemAdditionalAttributeLinks as $itemAdditionalAttributeLink) {
            $value = $this->getValueFromAttributesAndProduct($itemAdditionalAttributeLink, $product);

            if ($value) {
                $item->attributesValues()->updateOrCreate([
                    'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                ], [
                    'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                    'value' => $value
                ]);
            }
        }

    }

    public function prepareAttributes(MoyskladItemMainAttributeLink|MoyskladItemAdditionalAttributeLink $link, Product $product): int|bool|float|string|null
    {
        /** @var Attribute $attribute */
        if ($attribute = $product->getAttributes()->firstWhere(fn(Attribute $attribute) => $attribute->getId() === $link->link)) {
            if ($link->user_type === 'boolean') {
                return boolval($attribute->getValue());
            } elseif ($link->user_type === 'double') {
                return floatval($attribute->getValue());
            } elseif ($link->user_type === 'integer') {
                return intval(preg_replace("/[^0-9]/", "", $attribute->getValue()));
            } else if ($link->user_type === 'string') {
                return $attribute->getValue();
            }
        }

        return null;
    }

    public function getValueFromAttributesAndProduct(MoyskladItemMainAttributeLink|MoyskladItemAdditionalAttributeLink $link, Product $product): int|bool|float|string|null
    {
        if ($link->type === 'metadata') {
            return $this->prepareAttributes($link, $product);
        } else if ($link->type === 'object.value') {
            return $product->{'get' . Str::apa($link->link)}()->getValue();
        } else if ($link->type === 'main') {
            if ($link->user_type === 'boolean') {
                return boolval($product->{'is' . Str::apa($link->link)}());
            }
            return $product->{'get' . Str::apa($link->link)}();
        }

        return null;
    }
}