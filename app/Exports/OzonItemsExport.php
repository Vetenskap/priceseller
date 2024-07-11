<?php

namespace App\Exports;

use App\Imports\OzonItemsImport;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OzonItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public Collection $warehouses;

    public function __construct(public OzonMarket $market, public bool $template = false)
    {
        $this->warehouses = $this->market->warehouses;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->template) return collect();

        $allData = collect();

        $this->market->items()
            ->orderByDesc('updated_at')
            ->chunk(1000, function (Collection $items) use (&$allData) {
                $chunkData = $items->map(function (OzonItem $item) {
                    $main = [
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
                        'created_at' => $item->created_at,
                        'delete' => 'Нет'
                    ];

                    $main = array_merge($main, $this->warehouses->map(fn(OzonWarehouse $warehouse) => ['Склад ' . $warehouse->name => $item->stocks()->where('ozon_warehouse_id', $warehouse->id)->first()?->stock])
                        ->collapse()
                        ->all());

                    return $main;
                });

                $allData = $allData->merge($chunkData);
            });

        return $allData;
    }

    public function headings(): array
    {
        return array_merge(OzonItemsImport::HEADERS, $this->warehouses->map(fn(OzonWarehouse $warehouse) => 'Склад ' . $warehouse->name)->all());
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
