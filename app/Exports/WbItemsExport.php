<?php

namespace App\Exports;

use App\Models\WbMarket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WbItemsExport implements FromCollection, WithHeadings
{
    public function __construct(public WbMarket $market)
    {
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = $this->market->items->sortByDesc('updated_at');

        return $items->map(function ($item) {
            return [
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
                'created_at' => $item->created_at
            ];
        });
    }

    public function headings(): array
    {
        // Укажите свои собственные названия колонок
        return [
            'nmID',
            'vendorCode',
            'Код',
            'sku',
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
            'Создано'
        ];
    }
}
