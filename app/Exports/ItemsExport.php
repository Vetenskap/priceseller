<?php

namespace App\Exports;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public function __construct(public int $userId)
    {
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = Item::where('user_id', $this->userId)->when(App::isLocal(), function (Builder $query) {
            $query->limit(100);
        })->get()->sortByDesc('updated_at');

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
                'updated' => $item->updated ? 'Да' : 'Нет',
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'delete' => 'Нет'
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
            'Удалить'
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
