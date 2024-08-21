<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\Item\ItemService;
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
    CONST HEADERS = [
        'МС UUID',
        'Код',
        'Наименование',
        'Поставщик',
        'Артикул',
        'Бренд',
        'Цена',
        'Закупочная цена резерв',
        'Количество',
        'Кратность отгрузки',
        'Был обновлён',
        'Выгружать на ВБ',
        'Выгружать на ОЗОН',
        'Обновлён',
        'Создан',
        'Удалить'
    ];

    public int $correct = 0;
    public int $error = 0;
    public int $updated = 0;
    public int $deleted = 0;
    public User $user;

    public function __construct(int $userId)
    {
        $this->user = User::with('itemAttributes')->find($userId);
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

            if ($row->get('Удалить')) {

                $this->deleted++;

                $item->delete();

                return null;
            }

            $this->updated++;

            $row->where(function ($value, $key) {
                return str_contains($key, 'Доп. поле:');
            })->mapWithKeys(function ($value, $key) {
                return [str_replace('Доп. поле: ', '', $key) => $value];
            })->each(function ($value, $key) use ($item) {

                if ($userAttribute = $this->user->itemAttributes->where('name', $key)->first()) {

                    $item->attributesValues()->updateOrCreate([
                        'item_attribute_id' => $userAttribute->id,
                    ], [
                        'item_attribute_id' => $userAttribute->id,
                        'value' => $value
                    ]);
                }
            });

            $item->update([
                'ms_uuid' => $row->get('МС UUID'),
                'name' => $row->get('Наименование'),
                'supplier_id' => $supplier->id,
                'article' => $row->get('Артикул'),
                'brand' => $row->get('Бренд'),
                'multiplicity' => $row->get('Кратность отгрузки'),
                'unload_wb' => $row->get('Выгружать на ВБ'),
                'unload_ozon' => $row->get('Выгружать на ОЗОН'),
                'buy_price_reserve' => $row->get('Закупочная цена резерв')
            ]);

            return null;
        }

        if ($row->get('Удалить')) {

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
            'id' => Str::uuid(),
            'unload_wb' => $row->get('Выгружать на ВБ'),
            'unload_ozon' => $row->get('Выгружать на ОЗОН'),
            'buy_price_reserve' => $row->get('Закупочная цена резерв'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function prepareForValidation($data, $index)
    {
        if ($index % 1000 === 0) ItemsImportReportService::flush($this->user, $this->correct, $this->error, $this->updated, $this->deleted);

        $data['Кратность отгрузки'] = preg_replace("/[^0-9]/", "", $data['Кратность отгрузки']);
        $data['Выгружать на ВБ'] = $data['Выгружать на ВБ'] === 'Да';
        $data['Выгружать на ОЗОН'] = $data['Выгружать на ОЗОН'] === 'Да';
        $data['Удалить'] = $data['Удалить'] === 'Да';
        $data['Закупочная цена резерв'] = (float) str_replace(',', '.', $data['Закупочная цена резерв']);

        return $data;
    }

    public function rules(): array
    {
        return ItemService::excelImportRules();
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
