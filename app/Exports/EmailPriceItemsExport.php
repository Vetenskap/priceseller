<?php

namespace App\Exports;

use App\Models\EmailPriceItem;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmailPriceItemsExport implements FromCollection, WithHeadings
{
    public function __construct(public Supplier $supplier)
    {

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $allData = collect();

        $this->supplier->priceItems()->with('item')->chunk(1000, function (Collection $priceItems) use (&$allData) {
            $chunkData = $priceItems->map(function (EmailPriceItem $emailPriceItem) {
                return [
                    'article' => $emailPriceItem->article,
                    'brand' => $emailPriceItem->brand,
                    'price' => $emailPriceItem->price,
                    'stock' => $emailPriceItem->stock,
                    'status' => $emailPriceItem->message,
                    'item_code' => $emailPriceItem->item->code
                ];
            });
            $allData = $allData->merge($chunkData);
        });

        return $allData;
    }

    public function headings(): array
    {
        return [
            'Артикул',
            'Бренд',
            'Цена',
            'Остаток',
            'Статус',
            'Товар'
        ];
    }
}
