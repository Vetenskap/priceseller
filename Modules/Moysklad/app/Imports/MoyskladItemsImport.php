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
use Modules\Moysklad\Models\MoyskladItemMainAttributeLink;
use Modules\Moysklad\Models\MoyskladSupplierSupplier;
use Modules\Moysklad\Services\MoyskladService;

class MoyskladItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithValidation, WithBatchInserts, SkipsEmptyRows, SkipsOnFailure
{
    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public Collection $suppliers;
    public Moysklad $moysklad;
    public Collection $attributes;
    public array $rules;

    public function __construct(Moysklad $moysklad)
    {
        HeadingRowFormatter::default('slug');

        $moyskladSuppliers = (new MoyskladService($moysklad))->getAllSuppliers();

        $suppliers = [];
        $moysklad->suppliers->each(function (MoyskladSupplierSupplier $supplier) use (&$suppliers, $moyskladSuppliers) {
            $suppliers[collect($moyskladSuppliers)->firstWhere('id', $supplier->moysklad_supplier_uuid)['name']] = $supplier->supplier;
        });
        $this->suppliers = collect($suppliers);
        $this->moysklad = $moysklad;
        $this->attributes = $moysklad->itemMainAttributeLinks->pluck(null, 'attribute_name');
        $this->rules = [
            'uuid' => ['required', 'uuid'],
            $this->generateKey($this->attributes->get('code')) => ['required'],
            $this->generateKey($this->attributes->get('multiplicity')) => ['required', 'integer', 'min:1'],
            $this->generateKey($this->attributes->get('article')) => ['required'],
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $row = collect($row);

        dd($row);

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

    public function prepareForValidation($data)
    {
        $this->attributes->each(function (MoyskladItemMainAttributeLink $link) use (&$data) {
            $key = $this->generateKey($link);

            if ($link->user_type === 'double') {
                $data[$key] = floatval($data[$key]);
            } elseif ($link->user_type === 'boolean') {
                $data[$key] = $link->invert
                    ? !($data[$key] === 'да')
                    : ($data[$key] === 'да');
            } elseif ($link->user_type === 'integer') {
                $data[$key] = intval($data[$key]);
            }
        });

        return $data;
    }

    private function generateKey(MoyskladItemMainAttributeLink $link): string
    {
        $prefix = $link->type === 'metadata' ? 'dop_pole_' : '';
        return $prefix . $this->toSlug($link->link_label);
    }

    public function toSlug($value): string
    {
        return Str::slug($value, '_');
    }

    public function rules(): array
    {
        return $this->rules;
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
        return 10;
    }

    public function chunkSize(): int
    {
        return 10;
    }

}
