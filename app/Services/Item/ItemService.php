<?php

namespace App\Services\Item;

use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    public static function store(array $values, int $userId): array
    {
        $response = ['status' => 'success'];

        $validator = Validator::make($values, [
            'ms_uuid' => ['required', 'uuid'],
            'code' => ['required', 'string'],
            'name' => ['required', 'string'],
            'supplier_id' => ['required', 'uuid', 'exists:suppliers,ms_uuid'],
            'article' => ['required', 'string'],
            'brand' => ['nullable', 'string'],
            'multiplicity' => ['nullable', 'integer', 'min:1'],
        ], [
            'ms_uuid.required' => 'Поле обязательно',
            'ms_uuid.uuid' => 'Поле должно быть действительным UUID',
            'code.required' => 'Поле обязательно',
            'code.string' => 'Поле должно быть строкой',
            'code.name' => 'Поле должно быть строкой',
            'name.string' => 'Поле должно быть строкой',
            'name.name' => 'Поле должно быть строкой',
            'article.required' => 'Поле должно быть строкой',
            'article.string' => 'Поле должно быть строкой',
            'brand.string' => 'Поле должно быть строкой',
            'multiplicity.integer' => 'Поле должно быть целым числом',
            'multiplicity.min' => 'Поле должно быть не меньше 1',
            'supplier_id.required' => 'Поле обязательно',
            'supplier_id.uuid' => 'Поле должно быть действительным UUID',
            'supplier_id.exists' => 'Поставщик не найден в базе',
        ]);

        $errors = $validator->errors();

        if ($errors->count()) {

            return ['status' => 'error', 'errors' => $errors->messages()];

        }

        $data = $validator->validate();
        $data['user_id'] = $userId;
        $data['supplier_id'] = Supplier::where('ms_uuid', $data['supplier_id'])->first()->id;

        if (Item::where('ms_uuid', $data['ms_uuid'])->exists()) $response['status'] = 'updated';

        Item::updateOrCreate([
            'ms_uuid' => $data['ms_uuid']
        ], $data);

        return $response;
    }
}
