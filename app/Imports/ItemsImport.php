<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\ItemsImportReportService;
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

class ItemsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public int $deleted = 0;
    public User $user;
    public Collection $warehouses;

    public function __construct(int $userId)
    {
        $this->user = User::find($userId);
        $this->warehouses = $this->user->warehouses;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row = collect($row);

        $supplier = $this->user->suppliers()->where('name', $row->get('Поставщик'))->first();

        if (!$supplier) {

            ItemsImportReportService::addBadItem(
                $this->user,
                0,
                'Поставщик',
                ['Не найден поставщик'],
                $row->all()
            );

            $this->error++;

            return null;
        }

        if ($item = $this->user->items()->where('code', $row->get('Код'))->first()) {

            if ($row->get('Удалить') === 'Да') {

                $this->deleted++;

                $item->delete();

                return null;
            }

            $this->updated++;

            $item->update([
                'ms_uuid' => $row->get('МС UUID'),
                'name' => $row->get('Наименование'),
                'supplier_id' => $supplier->id,
                'article' => $row->get('Артикул'),
                'brand' => $row->get('Бренд'),
                'multiplicity' => $row->get('Кратность отгрузки'),
            ]);

            $this->warehouses->each(function (Warehouse $warehouse) use ($row, $item) {
                $stock = $row->get('Склад ' . $warehouse->name, 0);
                $item->warehousesStocks()->updateOrCreate([
                    'warehouse_id' => $warehouse->id,
                    'item_id' => $item->id,
                ], [
                    'warehouse_id' => $warehouse->id,
                    'stock' => $stock ?: 0,
                ]);
            });

            return null;
        }

        if ($row->get('Удалить') === 'Да') {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->user,
                0,
                'Удалить',
                ['Не удалось создать товар, стоит метка "Удалить"'],
                $row->all()
            );

            return null;
        }

        $this->correct++;

        return new Item([
            'ms_uuid' => $row->get('МС UUID'),
            'code' => $row->get('Код'),
            'name' => $row->get('Наименование'),
            'supplier_id' => $supplier->id,
            'article' => $row->get('Артикул'),
            'brand' => $row->get('Бренд'),
            'multiplicity' => $row->get('Кратность отгрузки'),
            'user_id' => $this->user->id,
            'id' => Str::uuid()
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->user, $this->correct, $this->error, $this->updated, $this->deleted);

        $data['Кратность отгрузки'] = preg_replace("/[^0-9]/", "", $data['Кратность отгрузки']);

        return $data;
    }

    public function rules(): array
    {
        return [
            'МС UUID' => ['nullable'],
            'Код' => ['required'],
            'Наименование' => ['nullable'],
            'Артикул' => ['required'],
            'Бренд' => ['nullable'],
            'Кратность отгрузки' => ['required', 'integer', 'min:1'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'Код.required' => 'Поле обязательно',
            'Артикул.required' => 'Поле обязательно',
            'Кратность отгрузки.required' => 'Поле обязательно',
            'Кратность отгрузки.integer' => 'Поле должно быть целым числом',
            'Кратность отгрузки.min' => 'Поле должно быть не меньше 1',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {

            $this->error++;

            ItemsImportReportService::addBadItem(
                $this->user,
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
