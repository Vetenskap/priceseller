<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\User;
use App\Services\ItemsMoyskladImportReportService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ItemsMsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;

    public User $user;
    public Collection $attributes;

    public function __construct(int $userId, Collection $attributes)
    {
        $this->user = User::findOrFail($userId);
        $this->attributes = $attributes;
    }

    public function model(array $row)
    {
        $row = collect($row);

        $supplier = $this->user->suppliers()->where('name', $row->get(Str::slug('Поставщик', '_')))->first();

        if (!$supplier) {

            ItemsMoyskladImportReportService::addBadItem(
                $this->user->moysklad,
                0,
                'Поставщик',
                ['Не найден поставщик'],
                $row->all()
            );

            $this->error++;

            return null;
        }

        if ($item = $this->user->items()->where('ms_uuid', $row->get('uuid'))->first()) {

            $this->updated++;

            $item->update([
                'ms_uuid' => $row->get('uuid'),
                'name' => $row->get(Str::slug($this->attributes->get('name'), '_')),
                'supplier_id' => $supplier->id,
                'article' =>  $row->get(Str::slug($this->attributes->get('article'), '_')),
                'brand' =>  $row->get(Str::slug($this->attributes->get('brand'), '_')),
                'multiplicity' =>  $row->get(Str::slug($this->attributes->get('multiplicity'), '_')),
                'code' =>  $row->get(Str::slug($this->attributes->get('code'), '_')),
            ]);
            return null;
        }

        $this->correct++;

        return new Item([
            'ms_uuid' => $row->get('uuid'),
            'name' => $row->get(Str::slug($this->attributes->get('name'), '_')),
            'supplier_id' => $supplier->id,
            'article' => $row->get(Str::slug($this->attributes->get('article'), '_')),
            'brand' => $row->get(Str::slug($this->attributes->get('brand'), '_')),
            'multiplicity' => $row->get(Str::slug($this->attributes->get('multiplicity'), '_')),
            'code' => $row->get(Str::slug($this->attributes->get('code'), '_')),
            'user_id' => $this->user->id,
            'id' => Str::uuid()
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsMoyskladImportReportService::flush($this->user->moysklad, $this->correct, $this->error, $this->updated);

        return $data;
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid'],
            Str::slug($this->attributes->get('code'), '_') => ['required', 'string'],
            Str::slug($this->attributes->get('name'), '_') => ['required', 'string'],
            Str::slug($this->attributes->get('article'), '_') => ['required', 'string'],
            Str::slug($this->attributes->get('brand'), '_') => ['nullable', 'string'],
            Str::slug($this->attributes->get('multiplicity'), '_') => ['required', 'integer', 'min:1'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            Str::slug($this->attributes->get('code'), '_') . ".required" => 'Поле обязательно',
            Str::slug($this->attributes->get('code'), '_') . ".string" => 'Поле должно быть строкой',
            Str::slug($this->attributes->get('name'), '_') . ".required" => 'Поле обязательно',
            Str::slug($this->attributes->get('name'), '_') . ".string" => 'Поле должно быть строкой',
            Str::slug($this->attributes->get('article'), '_') . ".required" => 'Поле обязательно',
            Str::slug($this->attributes->get('article'), '_') . ".string" => 'Поле должно быть строкой',
            Str::slug($this->attributes->get('multiplicity'), '_') . ".required" => 'Поле обязательно',
            Str::slug($this->attributes->get('multiplicity'), '_') . ".integer" => 'Поле должно быть целым числом',
            Str::slug($this->attributes->get('multiplicity'), '_') . ".min" => 'Поле не может быть меньше 1',
            Str::slug($this->attributes->get('brand'), '_') . ".string" => 'Поле должно быть строкой',
            "uuid.required" => 'Поле обязательно',
            "uuid.uuid" => 'Поле должно быть действительным UUID',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {

            Log::debug('fail', [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);

            $this->error++;

            ItemsMoyskladImportReportService::addBadItem(
                $this->user->moysklad,
                $failure->row(),
                $failure->attribute(),
                $failure->errors(),
                $failure->values()
            );

        }
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
