<?php

namespace App\Exports;

use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WbItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public Collection $warehouses;

    public function __construct(public WbMarket $market)
    {
        $this->warehouses = $this->market->warehouses;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = $this->market->items->sortByDesc('updated_at');

        return $items->map(function (WbItem $item) {
            $main = [
                'nmID' => $item->nm_id,
                'vendor_code' => $item->vendor_code,
                'item_code' => $item->item->code,
                'sku' => $item->sku,
                'sales_percent' => $item->sales_percent,
                'min_price' => $item->min_price,
                'retail_markup_percent' => $item->retail_markup_percent,
                'package' => $item->package,
                'volume' => $item->volume,
                'price' => $item->price,
                'price_market' => $item->price_market,
                'count' => $item->count,
                'item_price' => $item->item->price,
                'multiplicity' => $item->item->multiplicity,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'delete' => 'Нет'
            ];

            $main = array_merge($main, $this->warehouses->map(fn(WbWarehouse $warehouse) => ['Склад ' . $warehouse->name => $item->stocks()->where('wb_warehouse_id', $warehouse->id)->first()?->stock])
                ->collapse()
                ->all());

            return $main;
        });
    }

    public function headings(): array
    {
        // Укажите свои собственные названия колонок
        $main = [
            'Артикул WB (nmID)',
            'Артикул продавца (vendorCode)',
            'Код',
            'Баркод (sku)',
            'Комиссия, процент',
            'Мин. цена',
            'Розничная наценка, процент',
            'Упаковка',
            'Объем',
            'Новая цена',
            'Цена из маркета',
            'Остаток',
            'Закупочная цена',
            'Кратность отгрузки',
            'Обновлено',
            'Создано',
            'Удалить'
        ];

        return array_merge($main, $this->warehouses->map(fn(WbWarehouse $warehouse) => 'Склад ' . $warehouse->name)->all());
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
