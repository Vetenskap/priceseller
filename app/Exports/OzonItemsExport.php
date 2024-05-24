<?php

namespace App\Exports;

use App\Models\OzonMarket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OzonItemsExport implements FromCollection, WithHeadings
{
    public function __construct(public OzonMarket $market)
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
                'product_id' => $item->product_id,
                'offer_id' => $item->offer_id,
                'item_code' => $item->item->code,
                'min_price_percent' => $item->min_price_percent,
                'min_price' => $item->min_price,
                'shipping_processing' => $item->shipping_processing,
                'direct_flow_trans' => $item->direct_flow_trans,
                'deliv_to_customer' => $item->deliv_to_customer,
                'sales_percent' => $item->sales_percent,
                'price' => $item->price,
                'price_seller' => $item->price_seller,
                'price_min' => $item->price_min,
                'price_max' => $item->price_max,
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
        return [
            'product_id',
            'offer_id',
            'Код',
            'Мин. Цена, процент',
            'Мин. Цена',
            'Обработка отправления',
            'Магистраль',
            'Последняя миля',
            'Комиссия',
            'Цена продажи',
            'Цена конкурента',
            'Минимальная цена',
            'Цена до скидки, процент',
            'Цена из маркета',
            'Остаток',
            'Закупочная цена',
            'Кратность отгрузки',
            'Обновлено',
            'Загружено'
        ];
    }
}
