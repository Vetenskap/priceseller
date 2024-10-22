<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\Livewire\ModuleComponent;
use App\Models\Item;
use App\Models\WbItem;
use Illuminate\Support\Arr;
use Livewire\Component;

class AssemblyIndex extends ModuleComponent
{
    public $headingLevel = '3';

    public $headingButton = '3';

    public $fields = [];

    public $selectedFields = [];

    public $additionalFields = [];

    public function mount(): void
    {
        $this->fields['Поля товара'] = Arr::pluck(Item::MAINATTRIBUTES, 'label');
        $this->fields['Доп. поля товара'] = $this->currentUser()->itemAttributes()->pluck('name');
        $this->fields['Поля связи'] = Arr::pluck(WbItem::MAINATTRIBUTES, 'label');
        $this->fields['Поля заказа'] = [];
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
            }

            // Обновляем исходный массив
            $this->selectedFields = $newArray;
        }

    }

    public function addField($field): void
    {
        $this->selectedFields[$field] = [
            'level' => '1',
            'color' => '#000000'
        ];
    }

    public function addAdditionalField($field)
    {
        $this->additionalFields[$field] = [
            'level' => '1',
            'color' => '#000000'
        ];
    }


    public function render()
    {
        return view('assembly::livewire.assembly.assembly-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
