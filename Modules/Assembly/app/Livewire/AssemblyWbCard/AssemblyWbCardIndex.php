<?php

namespace Modules\Assembly\Livewire\AssemblyWbCard;

use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\Posting;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\Product;
use App\HttpClient\OzonClient\Resources\ProductInfoAttribute;
use App\Models\Item;
use App\Models\ItemAttribute;
use App\Models\WbItem;
use Illuminate\Support\Arr;
use Livewire\Component;
use function Modules\Assembly\Livewire\AssemblyOzonCard\addTypeToAttributes;

class AssemblyWbCardIndex extends Component
{
    public $fields = [];

    public $mainFields = [];

    public $selectedFields = [];

    public $selectedAdditionalFields = [];

    public function mount(): void
    {
        $this->fields['Поля товара (ps)'] = Arr::map(Item::MAINATTRIBUTES, function ($item) {
            return Arr::add($item, 'type', 'item');
        });
        $this->fields['Доп. поля товара (ps)'] = $this->currentUser()->itemAttributes->map(function (ItemAttribute $attribute) {
            return ['name' => $attribute->id, 'label' => $attribute->name, 'type' => 'attribute_item'];
        })->toArray();
        $this->fields['Поля товара (связь)'] = Arr::map(WbItem::MAINATTRIBUTES, function ($item) {
            return Arr::add($item, 'type', 'product');
        });
        $this->fields['Поля заказа'] = addTypeToAttributes(Posting::ATTRIBUTES, 'order');
        $this->fields['Поля товара (заказ)'] = Arr::map(Product::ATTRIBUTES, function ($item) {
            return Arr::add($item, 'type', 'order_product');
        });
        $this->fields['Атрибуты товара (заказ)'] = Arr::map(ProductInfoAttribute::ATTRIBUTES, function ($item) {
            return Arr::add($item, 'type', 'order_attribute_product');
        });

        $this->selectedFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'wb')
            ->whereNot('type', 'main')
            ->where('additional', false)
            ->orderBy('index')
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->selectedAdditionalFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'wb')
            ->where('additional', true)
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->mainFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'wb')
            ->where('type', 'main')
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        if (!isset($this->mainFields['name_heading'])) {
            $this->mainFields['name_heading'] = [
                'size_level' => '1',
                'market' => 'wb',
                'type' => 'main'
            ];
        }

        if (!isset($this->mainFields['button_label'])) {
            $this->mainFields['button_label'] = [
                'size_level' => '1',
                'market' => 'wb',
                'type' => 'main'
            ];
        }
    }

    public function save(): void
    {
        foreach ($this->selectedFields as $field => $parameters) {
            $this->currentUser()->assemblyProductSettings()->updateOrCreate([
                'field' => $field,
                'market' => $parameters['market']
            ], array_merge($parameters, [
                'field' => $field,
                'label' => $parameters['label'],
            ]));
        }

        foreach ($this->mainFields as $field => $parameters) {
            $this->currentUser()->assemblyProductSettings()->updateOrCreate([
                'field' => $field,
                'market' => $parameters['market']
            ], array_merge($parameters, [
                'field' => $field,
            ]));
        }

        foreach ($this->selectedAdditionalFields as $field => $parameters) {
            $this->currentUser()->assemblyProductSettings()->updateOrCreate([
                'field' => $field,
                'market' => $parameters['market']
            ], array_merge($parameters, [
                'field' => $field,
                'label' => $parameters['label'],
            ]));
        }
    }

    public function upOrDown($field): string
    {
        $keys = array_keys($this->selectedFields);

        $index = array_search($field, $keys);

        if ($index !== false && $index > 0) {
            return 'up';
        } else {
            return 'down';
        }
    }

    public function upField($field)
    {
        // Преобразуем массив ключей
        $keys = array_keys($this->selectedFields);

        // Находим индекс ключа, который нужно переместить
        $index = array_search($field, $keys);

        // Если ключ найден и он не первый
        if ($index !== false && $index > 0) {

            // Меняем местами ключи в массиве
            $previousKey = $keys[$index - 1];
            $keys[$index - 1] = $field;
            $keys[$index] = $previousKey;

            // Создаем новый упорядоченный массив с обновленными ключами
            $newArray = [];
            foreach ($keys as $key) {
                $newArray[$key] = $this->selectedFields[$key];
                if ($key == $field) {
                    $newArray[$key]['index'] = $this->selectedFields[$key]['index'] - 1;
                } else {
                    $newArray[$key]['index'] = $this->selectedFields[$key]['index'] + 1;
                }
            }

            // Обновляем исходный массив
            $this->selectedFields = $newArray;
        } else {

            // Меняем местами ключи в массиве
            $previousKey = $keys[$index + 1];
            $keys[$index + 1] = $field;
            $keys[$index] = $previousKey;

            // Создаем новый упорядоченный массив с обновленными ключами
            $newArray = [];
            foreach ($keys as $key) {
                $newArray[$key] = $this->selectedFields[$key];
                if ($key == $field) {
                    $newArray[$key]['index'] = $this->selectedFields[$key]['index'] + 1;
                } else {
                    $newArray[$key]['index'] = $this->selectedFields[$key]['index'] - 1;
                }
            }

            // Обновляем исходный массив
            $this->selectedFields = $newArray;
        }
    }

    public function addField($field): void
    {
        $this->selectedFields[$field['name']] = array_merge($field, [
            'size_level' => '1',
            'color' => '#000000',
            'additional' => false,
            'market' => 'wb'
        ]);

        $keys = array_keys($this->selectedFields);

        $index = array_search($field['name'], $keys);

        $this->selectedFields[$field['name']] = array_merge($this->selectedFields[$field['name']], [
            'index' => $index
        ]);
    }

    public function addAdditionalField($field): void
    {
        $this->selectedAdditionalFields[$field['name']] = array_merge($field, [
            'size_level' => '1',
            'color' => '#000000',
            'additional' => true,
            'market' => 'wb'
        ]);
    }

    public function render()
    {
        return view('assembly::livewire.assembly-wb-card.assembly-wb-card-index');
    }
}