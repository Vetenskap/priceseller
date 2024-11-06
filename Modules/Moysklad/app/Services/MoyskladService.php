<?php

namespace Modules\Moysklad\Services;

use App\Models\BundleItem;
use App\Models\Item;
use App\Services\Item\ItemService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Moysklad\HttpClient\Resources\Context\CompanySettings\PriceType;
use Modules\Moysklad\HttpClient\Resources\Entities\Bundle\Bundle;
use Modules\Moysklad\HttpClient\Resources\Entities\Bundle\MetaArrays\Component;
use Modules\Moysklad\HttpClient\Resources\Entities\Counterparty;
use Modules\Moysklad\HttpClient\Resources\Entities\EntityList;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Metadata\Attribute;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Organization;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;
use Modules\Moysklad\HttpClient\Resources\Entities\Store;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladBundleMainAttributeLink;
use Modules\Moysklad\Models\MoyskladItemAdditionalAttributeLink;
use Modules\Moysklad\Models\MoyskladItemMainAttributeLink;
use Modules\Moysklad\Models\MoyskladQuarantine;

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

    public function getAllAssortmentAttributes(): Collection
    {
        $assortmentAttributes = Cache::tags(['moysklad', 'assortment', 'attributes'])->remember($this->moysklad->id, now()->addDay(), function () {

            $entityList = new EntityList(Attribute::class, $this->moysklad->api_key);

            $allAttributes = collect();

            do {

                $attributes = $entityList->getNext()->map(function (Attribute $attribute) {
                    return ['name' => $attribute->getId(), 'label' => $attribute->getName(), 'type' => 'metadata'];
                });

                $allAttributes = $allAttributes->merge($attributes);

            } while ($entityList->hasNext());

            return $allAttributes;

        });

        return $assortmentAttributes->merge(Product::FIELDS);
    }

    public function getAllPriceTypes(): Collection
    {
        return PriceType::fetchAll($this->moysklad->api_key)->map(function (PriceType $priceType) {
            return ['name' => $priceType->id, 'label' => $priceType->getName()];
        });
    }

    public function getAllBundleAttributes(): Collection
    {
        $assortmentAttributes = Cache::tags(['moysklad', 'assortment', 'attributes'])->remember($this->moysklad->id, now()->addDay(), function () {

            $entityList = new EntityList(Attribute::class, $this->moysklad->api_key);

            $allAttributes = collect();

            do {

                $attributes = $entityList->getNext()->map(function (Attribute $attribute) {
                    return ['name' => $attribute->getId(), 'label' => $attribute->getName(), 'type' => 'metadata'];
                });

                $allAttributes = $allAttributes->merge($attributes);

            } while ($entityList->hasNext());

            return $allAttributes;

        });

        return $assortmentAttributes->merge(Bundle::FIELDS);
    }

    public function importApiItems(): void
    {
        $offset = Cache::tags(['moysklad', 'product', 'offset'])->get($this->moysklad->id, 0);

        $entityList = new EntityList(Product::class, $this->moysklad->api_key, offset: $offset);

        do {

            $products = $entityList->getNext();

            $products->each(function (Product $product) use (&$dirtyItems) {

                /** @var MoyskladItemMainAttributeLink $itemMainAttributeLink */
                $itemMainAttributeLink = $this->moysklad->itemMainAttributeLinks->where('attribute_name', 'code')->first();

                $code = static::getValueFromAttributesAndProduct($itemMainAttributeLink->type, $itemMainAttributeLink->link, $product, $itemMainAttributeLink->user_type, $itemMainAttributeLink->invert, $itemMainAttributeLink->attribute_name);

                if ($item = $this->moysklad->user->items()->where('ms_uuid', $product->id)
                    ->orWhere('code', $code)
                    ->first()
                ) {
                    $this->updateItemFromProduct($product, $item);
                } else {
                    $this->createItemFromProduct($product);
                }

            });

            Cache::tags(['moysklad', 'product', 'offset'])->set($this->moysklad->id, $entityList->getOffset(), now()->addDay());

        } while ($entityList->hasNext());
    }

    public function importApiBundles()
    {
        $offset = Cache::tags(['moysklad', 'bundle', 'offset'])->get($this->moysklad->id, 0);

        $entityList = new EntityList(Bundle::class, $this->moysklad->api_key, offset: $offset, limit: 100, queryParameters: ['expand' => 'components']);

        do {

            $bundles = $entityList->getNext();

            $bundles->each(function (Bundle $bundle) use (&$dirtyItems) {

                /** @var MoyskladBundleMainAttributeLink $bundleMainAttributeLink */
                $bundleMainAttributeLink = $this->moysklad->bundleMainAttributeLinks->where('attribute_name', 'code')->first();

                $code = static::getValueFromAttributesAndProduct($bundleMainAttributeLink->type, $bundleMainAttributeLink->link, $bundle, $bundleMainAttributeLink->user_type, link_attribute_name: $bundleMainAttributeLink->attribute_name);

                if ($userBundle = $this->moysklad->user->bundles()->where('ms_uuid', $bundle->id)
                    ->orWhere('code', $code)
                    ->first()
                ) {
                    $this->updateBundle($bundle, $userBundle);
                } else {
                    $this->createBundle($bundle);
                }

            });

            Cache::tags(['moysklad', 'product', 'offset'])->set($this->moysklad->id, $entityList->getOffset(), now()->addDay());

        } while ($entityList->hasNext());
    }

    public function createItemFromProduct(Product $product): ?Item
    {
        $itemService = new ItemService($this->moysklad->user);

        $supplier = $this->moysklad->suppliers->where('moysklad_supplier_uuid', $product->getSupplier()?->id)->first()?->supplier;

        if ($supplier) {

            $data['supplier_id'] = $supplier->id;
            $data['ms_uuid'] = $product->id;
            $data['unload_ozon'] = true;
            $data['unload_wb'] = true;

            foreach ($this->moysklad->itemMainAttributeLinks as $itemMainAttributeLink) {
                $value = static::getValueFromAttributesAndProduct($itemMainAttributeLink->type, $itemMainAttributeLink->link, $product, $itemMainAttributeLink->user_type, $itemMainAttributeLink->invert, $itemMainAttributeLink->attribute_name);

                $data[$itemMainAttributeLink->attribute_name] = $value;
            }

            /** @var MoyskladItemAdditionalAttributeLink $itemAdditionalAttributeLink */
            foreach ($this->moysklad->itemAdditionalAttributeLinks as $itemAdditionalAttributeLink) {

                $value = static::getValueFromAttributesAndProduct($itemAdditionalAttributeLink->type, $itemAdditionalAttributeLink->link, $product, $itemAdditionalAttributeLink->user_type, $itemAdditionalAttributeLink->invert);

                if ($value) {

                    if (!isset($data['attributes'])) {
                        $data['attributes'] = [];
                    }

                    $data['attributes'][] = [
                        'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                        'value' => $value
                    ];
                }
            }

            return $itemService->createFromMs($data);
        }

        return  null;
    }

    public function updateItemFromProductWithUpdatedFields(Product $product, Item $item, Collection $updatedFields): void
    {
        $updatedFields->each(function (string $updatedField) use ($product, $item) {

            /** @var MoyskladItemMainAttributeLink $itemMainAttributeLink */
            if ($itemMainAttributeLink = $this->moysklad->itemMainAttributeLinks->where('link_name', $updatedField)->first()) {
                $value = static::getValueFromAttributesAndProduct($itemMainAttributeLink->type, $itemMainAttributeLink->link, $product, $itemMainAttributeLink->user_type, $itemMainAttributeLink->invert, $itemMainAttributeLink->attribute_name);

                $item->{$itemMainAttributeLink->attribute_name} = $value;

                try {
                    $item->save();
                } catch (\Throwable $e) {
                    report($e);
                    return;
                }
            }

            /** @var MoyskladItemAdditionalAttributeLink $itemAdditionalAttributeLink */
            if ($itemAdditionalAttributeLink = $this->moysklad->itemAdditionalAttributeLinks->where('link_name', $updatedField)->first()) {

                $value = static::getValueFromAttributesAndProduct($itemAdditionalAttributeLink->type, $itemAdditionalAttributeLink->link, $product, $itemAdditionalAttributeLink->user_type, $itemAdditionalAttributeLink->invert);

                $item->attributesValues()->updateOrCreate([
                    'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                ], [
                    'item_attribute_id' => $itemAdditionalAttributeLink->item_attribute_id,
                    'value' => $value
                ]);
            }

        });
    }

    public function createBundle(Bundle $bundle): void
    {
        $data['ms_uuid'] = $bundle->id;

        foreach ($this->moysklad->bundleMainAttributeLinks as $bundleMainAttributeLink) {

            $value = static::getValueFromAttributesAndProduct($bundleMainAttributeLink->type, $bundleMainAttributeLink->link, $bundle, $bundleMainAttributeLink->user_type, link_attribute_name: $bundleMainAttributeLink->attribute_name);

            $data[$bundleMainAttributeLink->attribute_name] = $value;

        }

        try {
            $userBundle = $this->moysklad->user->bundles()->create($data);
        } catch (\Throwable $e) {
            report($e);
            return;
        }

        $bundle->getComponents()->each(function (Component $component) use ($userBundle) {

            $supplierBundle = $userBundle->items()->first()?->supplier;

            if ($item = $this->moysklad->user->items()->where('ms_uuid', $component->getAssortment()->id)->first()) {

                if ($supplierBundle && $supplierBundle->id !== $item->supplier->id) {
                    // TODO: supplier bundle moysklad
                    return;
                }

                $userBundle->items()->attach($item->id, [
                    'multiplicity' => $component->getQuantity(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $component->getAssortment()->fetch($this->moysklad->api_key);
                if ($item = $this->createItemFromProduct($component->getAssortment())) {

                    if ($supplierBundle && $supplierBundle->id !== $item->supplier->id) {
                        // TODO: supplier bundle moysklad
                        return;
                    }

                    $userBundle->items()->attach($item->id, [
                        'multiplicity' => $component->getQuantity(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        });
    }

    public function updateBundle(Bundle $bundle, \App\Models\Bundle $userBundle): void
    {
        $userBundle->ms_uuid = $bundle->id;

        foreach ($this->moysklad->bundleMainAttributeLinks as $bundleMainAttributeLink) {

            $value = static::getValueFromAttributesAndProduct($bundleMainAttributeLink->type, $bundleMainAttributeLink->link, $bundle, $bundleMainAttributeLink->user_type, link_attribute_name: $bundleMainAttributeLink->attribute_name);

            $userBundle->{$bundleMainAttributeLink->attribute_name} = $value;

        }

        try {
            $userBundle->save();
        } catch (\Throwable $e) {
            report($e);
            return;
        }

        $userBundle->items()->each(function (Item $item) use ($bundle, $userBundle) {
            if (!$bundle->getComponents()->first(fn (Component $component) => $component->getAssortment()->id == $item->ms_uuid)) {
                $userBundle->items()->detach($item->id);
            }
        });

        $bundle->getComponents()->each(function (Component $component) use ($userBundle) {

            /** @var BundleItem $bundleItem */
            if ($bundleItem = $userBundle->items()->where('ms_uuid', $component->getAssortment()?->id)->first()?->pivot) {

                $bundleItem->update([
                    'multiplicity' => $component->getQuantity(),
                ]);

            } else {

                $supplierBundle = $userBundle->items()->first()?->supplier;

                if ($item = $this->moysklad->user->items()->where('ms_uuid', $component->getAssortment()->id)->first()) {

                    if ($supplierBundle && $supplierBundle->id !== $item->supplier->id) {
                        // TODO: supplier bundle moysklad
                        return;
                    }

                    $userBundle->items()->attach($item->id, [
                        'multiplicity' => $component->getQuantity(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {

                    $component->getAssortment()->fetch($this->moysklad->api_key);

                    if ($item = $this->createItemFromProduct($component->getAssortment())) {

                        if ($supplierBundle && $supplierBundle->id !== $item->supplier->id) {
                            // TODO: supplier bundle moysklad
                            return;
                        }

                        $userBundle->items()->attach($item->id, [
                            'multiplicity' => $component->getQuantity(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

            }

        });
    }

    public function updateItemFromProduct(Product $product, Item $item): void
    {
        $item->ms_uuid = $product->id;
        $item->unload_wb = true;
        $item->unload_ozon = true;

        foreach ($this->moysklad->itemMainAttributeLinks as $itemMainAttributeLink) {

            $value = static::getValueFromAttributesAndProduct($itemMainAttributeLink->type, $itemMainAttributeLink->link, $product, $itemMainAttributeLink->user_type, $itemMainAttributeLink->invert, $itemMainAttributeLink->attribute_name);

            $item->{$itemMainAttributeLink->attribute_name} = $value;

        }

        try {
            $item->save();
        } catch (\Throwable $e) {
            report($e);
            return;
        }

        foreach ($this->moysklad->itemAdditionalAttributeLinks as $itemAdditionalAttributeLink) {
            $value = static::getValueFromAttributesAndProduct($itemAdditionalAttributeLink->type, $itemAdditionalAttributeLink->link, $product, $itemAdditionalAttributeLink->user_type, $itemAdditionalAttributeLink->invert);

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

    public static function prepareAttributes($link, Product|Bundle $product, $link_user_type = null, $link_invert = null, $link_attribute_name = null): int|bool|float|string|null
    {
        /** @var Attribute $attribute */
        if ($attribute = $product->getAttributes()->firstWhere(fn(Attribute $attribute) => $attribute->getId() === $link)) {
            if ($link_user_type === 'boolean') {
                $value = boolval($attribute->getValue());
                return $link_invert ? !$value : $value;
            } elseif ($link_user_type === 'double') {
                return floatval($attribute->getValue());
            } elseif ($link_user_type === 'integer') {
                return intval(preg_replace("/[^0-9]/", "", $attribute->getValue()));
            } else if ($link_user_type === 'string') {
                return $attribute->getValue();
            }
        }

        if ($link_attribute_name === 'unload_ozon' || $link_attribute_name === 'unload_wb') {
            return true;
        }

        return null;
    }

    public static function getValueFromAttributesAndProduct($link_type, $link, Product|Bundle $product, $link_user_type = null, $link_invert = null, $link_attribute_name = null): int|bool|float|string|null
    {
        if ($link_type === 'metadata') {
            return self::prepareAttributes($link, $product, $link_user_type, $link_invert, $link_attribute_name);
        } else if ($link_type === 'object.value') {
            return $product->{'get' . Str::apa($link)}()->getValue();
        } else if ($link_type === 'main') {
            if ($link_user_type === 'boolean') {
               $value = boolval($product->{'is' . Str::apa($link)}());

               return $link_invert ? !$value : $value;
            }
            return $product->{'get' . Str::apa($link)}();
        }

        return null;
    }

    public function getAllOrganizations(): Collection
    {
        return Cache::tags(['moysklad', 'organizations'])->remember($this->moysklad->id, now()->addDay(), function () {

            $entityList = new EntityList(Organization::class, $this->moysklad->api_key);

            $allOrganizations = collect();

            do {

                $organizations = $entityList->getNext()->map(function (Organization $organization) {
                    return ['id' => $organization->id, 'name' => $organization->getName()];
                });

                $allOrganizations = $allOrganizations->merge($organizations);

            } while ($entityList->hasNext());

            return $allOrganizations;

        });
    }

    public function setBuyPriceAllQuarantine(HasMany $query): void
    {
        $query->chunk(1000, function (Collection $items) {

            $updateMassive = [];

            $items->each(function (MoyskladQuarantine $item) use (&$updateMassive) {
                $productEntity = new Product();
                $productEntity->setId($item->item->ms_uuid);
                $productEntity->getBuyPrice()->setValue($item->supplier_buy_price);
                $updateMassive[] = $productEntity->arrayToMassiveUpdate(['buyPrice']);
            });

            $result = Product::updateMassive($this->moysklad, $updateMassive)->toCollectionSpread();

            $result->each(function (Collection $item) {
                $item = Item::where('ms_uuid', $item->get('id'))->first();
                if ($item) {
                    $quarantine = $item->msQuarantine;
                    if ($quarantine) {
                        $quarantine->item()->update([
                            'buy_price_reserve' => $quarantine->supplier_buy_price
                        ]);
                        $item->buy_price_reserve = $quarantine->supplier_buy_price;
                        $quarantine->delete();
                    }
                }
            });

        });
    }

    public function setBuyPriceFromQuarantine(MoyskladQuarantine $quarantine): bool
    {
        $productEntity = new Product();
        $productEntity->setId($quarantine->item->ms_uuid);
        $productEntity->getBuyPrice()->setValue($quarantine->supplier_buy_price);
        $status = $productEntity->update($this->moysklad->api_key, ['buyPrice' => []]);
        if ($status) {
            $quarantine->item()->update([
                'buy_price_reserve' => $quarantine->supplier_buy_price
            ]);
            $quarantine->delete();
        }
        return $status;
    }
}
