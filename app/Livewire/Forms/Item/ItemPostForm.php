<?php

namespace App\Livewire\Forms\Item;

use App\Livewire\Components\Toast;
use App\Models\Item;
use App\Models\ItemAttributeValue;
use Illuminate\Support\Collection;
use Livewire\Form;

class ItemPostForm extends Form
{
    public ?Item $item;

    public $ms_uuid;

    public $code;

    public $article;

    public $brand;

    public $multiplicity;

    public $name;

    public $supplier_id;

    public $unload_wb;

    public $unload_ozon;

    public $attributes;

    public $buy_price_reserve = 0;

    public function setItem(Item $item)
    {
        $this->item = $item;
        $this->ms_uuid = $item->ms_uuid;
        $this->code = $item->code;
        $this->brand = $item->brand;
        $this->multiplicity = $item->multiplicity;
        $this->article = $item->article;
        $this->name = $item->name;
        $this->supplier_id = $item->supplier_id;
        $this->unload_ozon = $item->unload_ozon;
        $this->unload_wb = $item->unload_wb;
        $this->attributes = $item->attributesValues->mapWithKeys(function (ItemAttributeValue $attributeValue) {
            $value = $attributeValue->value;
            if ($attributeValue->attribute->type === 'boolean') {
                $value = boolval($value);
            }
            return [$attributeValue->item_attribute_id => $value];
        });
        $this->buy_price_reserve = $item->buy_price_reserve;
    }

    public function update(): Collection
    {
        if (!$this->supplier_id) {
            return collect(['status' => false, 'message' => 'Не выбран поставщик']);
        }

        collect($this->attributes)->each(function ($value, $key) {
            ItemAttributeValue::updateOrCreate([
                'item_attribute_id' => $key,
                'item_id' => $this->item->id
            ], [
                'item_attribute_id' => $key,
                'value' => $value,
                'item_id' => $this->item->id
            ]);
        });

        $this->item->update($this->except(['item', 'attributes']));

        return collect(['status' => true, 'message' => '']);
    }

    public function delete()
    {
        $this->item->delete();
    }
}
