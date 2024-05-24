<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsExport implements FromCollection, WithHeadings
{
    public function __construct(public int $userId)
    {
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = Item::where('user_id', $this->userId)->get()->sortByDesc('updated_at');

        return $items->map(function (Item $item) {
            return [
                'ms_uuid' => $item->ms_uuid,
                'code' => $item->code,
                'name' => $item->name,
                'supplier_name' => $item->supplier?->name,
                'article' => $item->article,
                'brand' => $item->brand,
                'price' => $item->price,
                'count' => $item->count,
                'multiplicity' => $item->multiplicity,
                'updated' => $item->updated,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at
            ];
        });
    }

    public function headings(): array
    {
        return [
            'МС UUID',
            'Код',
            'Наименование',
            'Поставщик',
            'Артикул',
            'Бренд',
            'Цена',
            'Количество',
            'Кратность отгрузки',
            'Был обновлён',
            'Обновлён',
            'Создан',
        ];
    }
}
