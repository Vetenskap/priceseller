<?php

namespace Modules\Moysklad\Imports;

use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;
use Modules\Moysklad\Models\Moysklad;
use Modules\Moysklad\Models\MoyskladSupplierSupplier;

class MoyskladItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public User $user;
    public Collection $attributes;
    public Collection $suppliers;

    public function __construct(int $userId, Collection $attributes, Moysklad $moysklad)
    {
        $this->user = User::find($userId);
        $this->attributes = $attributes;
        HeadingRowFormatter::default('slug');

        $suppliers = [];
        $moysklad->suppliers->each(function (MoyskladSupplierSupplier $supplier) use (&$suppliers) {
            $suppliers[$supplier->moysklad_supplier_name] = $supplier->supplier;
        });
        $this->suppliers = collect($suppliers);
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $row = collect($row);

        $supplier = $this->suppliers->get($row->get(Str::slug('Поставщик', '_')));

        if (!$supplier) {

//            ItemsImportReportService::addBadItem(
//                $this->user,
//                0,
//                'Поставщик',
//                ['Не найден поставщик'],
//                $row->all()
//            );

            $this->error++;

            return null;
        }

        if ($item = $this->user->items()->where('code', $row->get($this->attributes->get('code')))->orWhere('ms_uuid', $row->get('uuid'))->first()) {

            $this->updated++;

            $item->update([
                'ms_uuid' => $row->get('uuid'),
                'name' => $row->get($this->attributes->get('name')),
                'supplier_id' => $supplier->id,
                'article' => $row->get($this->attributes->get('article')),
                'brand' => $row->get($this->attributes->get('brand')),
                'multiplicity' => $row->get($this->attributes->get('multiplicity')),
                'unload_wb' => $row->get($this->attributes->get('unload_wb')),
                'unload_ozon' => $row->get($this->attributes->get('unload_ozon')),
            ]);

            return null;
        }

        $this->correct++;

        return new Item([
            'ms_uuid' => $row->get('uuid'),
            'code' => $row->get($this->attributes->get('code')),
            'name' => $row->get($this->attributes->get('name')),
            'supplier_id' => $supplier->id,
            'article' => $row->get($this->attributes->get('article')),
            'brand' => $row->get($this->attributes->get('brand')),
            'multiplicity' => $row->get($this->attributes->get('multiplicity')),
            'user_id' => $this->user->id,
            'id' => Str::uuid(),
            'unload_wb' => $row->get($this->attributes->get('unload_wb')),
            'unload_ozon' => $row->get($this->attributes->get('unload_ozon')),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    public function prepareForValidation($data, $index)
    {
//        if ($index % 1000 === 0) ItemsImportReportService::flush($this->user, $this->correct, $this->error, $this->updated);

        $data[$this->attributes->get('multiplicity')] = preg_replace("/[^0-9]/", "", $data[$this->attributes->get('multiplicity')]);
        $data[$this->attributes->get('unload_wb')] = $data[$this->attributes->get('unload_wb')] === 'Да';
        $data[$this->attributes->get('unload_ozon')] = $data[$this->attributes->get('unload_ozon')] === 'Да';

        return $data;
    }

    public function rules(): array
    {
        return [
            'uuid' => ['required', 'uuid'],
            $this->attributes->get('code') => ['required'],
            $this->attributes->get('name') => ['required'],
            $this->attributes->get('article') => ['required'],
            $this->attributes->get('brand') => ['nullable'],
            $this->attributes->get('multiplicity') => ['required', 'integer', 'min:1'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {

            $this->error++;

//            ItemsImportReportService::addBadItem(
//                $this->user,
//                $failure->row(),
//                $failure->attribute(),
//                $failure->errors(),
//                $failure->values()
//            );

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
