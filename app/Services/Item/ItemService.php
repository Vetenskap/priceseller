<?php

namespace App\Services\Item;

use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ItemService
{
    const PATH = "users/main/";
    const FILENAME = "priceseller";

    public function __construct(public User $user)
    {
    }

    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new ItemsExport($this->user->id), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }

    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new ItemsImport($this->user->id);

        Log::debug('Импорт прямо сейчас..');

        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');

        Log::debug('Импорт завершён, возвращаем результат');

        return collect([
            'correct' => $import->correct,
            'error' => $import->error,
            'updated' => $import->updated,
            'deleted' => $import->deleted,
        ]);
    }

    public function create(array $data): Item
    {

    }

    public function createFromMs(array $data): ?Collection
    {
        $data = Validator::make($data, static::moyskladImportRules($this->user->id));

        $errors = $data->errors();

        if ($errors->isNotEmpty()) {
            logger(json_encode($errors, JSON_UNESCAPED_UNICODE));
        }

        $created = collect();

        $items = $data->validate()['items'];

        foreach ($items as $item) {

            logger('item');
            logger($item);

            logger('new Item');
            logger($item);

            $newItem = $this->user->items()->create($item);

            logger('new Item');
            logger($newItem);

            foreach ($item['attributes'] as $attribute) {
                $newItem->attributesValues()->updateOrCreate([
                    'item_attribute_id' => $attribute['attribute_id'],
                ], [
                    'item_attribute_id' => $attribute['attribute_id'],
                    'value' => $attribute['value']
                ]);
            }

            $created->push($newItem);
        }

        return $created;
    }

    public static function moyskladImportRules(int $userId): array
    {
        return [
            'items' => ['array', 'max:1000'],
            'items.*.ms_uuid' => ['required', 'uuid', 'unique:items,ms_uuid'],
            'items.*.code' => [
                'required',
                Rule::unique('items', 'code')->where('user_id', $userId),
            ],
            'items.*.name' => ['nullable'],
            'items.*.supplier_id' => ['required', 'exists:suppliers,id'],
            'items.*.article' => ['required'],
            'items.*.brand' => ['nullable'],
            'items.*.count' => ['nullable', 'integer'],
            'items.*.multiplicity' => ['required', 'integer'],
            'items.*.unload_wb' => ['nullable', 'boolean'],
            'items.*.unload_ozon' => ['nullable', 'boolean'],
            'items.*.buy_price_reserve' => ['nullable', 'numeric'],
            'items.*.attributes' => ['nullable', 'array'],
            'items.*.attributes.*.attribute_id' => ['required', 'exists:item_attributes,id'],
            'items.*.attributes.*.value' => ['required'],
        ];
    }

    public static function excelImportRules(): array
    {
        return [
            'МС UUID' => ['nullable'],
            'Код' => ['required'],
            'Наименование' => ['nullable'],
            'Артикул' => ['required'],
            'Бренд' => ['nullable'],
            'Кратность отгрузки' => ['required', 'integer', 'min:1'],
            'Закупочная цена резерв' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
