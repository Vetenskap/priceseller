<?php

namespace App\Exports;

use App\Imports\WbItemsImport;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Services\ModuleService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Order\Models\Order;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WbItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public Collection $warehouses;
    public User $user;

    public function __construct(public WbMarket $market, public bool $template = false)
    {
        $this->warehouses = $this->market->warehouses;
        $this->user = $this->market->user;
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
                $chunkData = $items->map(function (WbItem $item) {
                    $main = [
                        'nmID' => $item->nm_id,
                        'vendor_code' => $item->vendor_code,
                        'item_code' => $item->wbitemable->code,
                        'item_type' => $item->wbitemable->getMorphClass() === 'App\Models\Item' ? 'Товар' : 'Комплект',
                        'sku' => $item->sku,
                        'sales_percent' => $item->sales_percent,
                        'min_price' => $item->min_price,
                        'retail_markup_percent' => $item->retail_markup_percent,
                        'package' => $item->package,
                        'volume' => $item->volume,
                        'price' => $item->price,
                        'price_market' => $item->price_market,
                        'count' => $item->count,
                        'item_price' => $item->wbitemable->getMorphClass() === 'App\Models\Item' ? $item->wbitemable->price : $item->wbitemable->items()->sum('price'),
                        'item_buy_price_reserve' => $item->wbitemable->getMorphClass() === 'App\Models\Item' ? $item->wbitemable->buy_price_reserve : $item->wbitemable->items()->sum('buy_price_reserve'),
                        'multiplicity' => $item->wbitemable->getMorphClass() === 'App\Models\Item' ? $item->wbitemable->multiplicity : $item->wbitemable->items()->min('bundle_items.multiplicity'),
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at,
                        'delete' => 'Нет'
                    ];

                    $main = array_merge($main, $this->warehouses->map(fn(WbWarehouse $warehouse) => ['Склад ' . $warehouse->name => $item->stocks()->where('wb_warehouse_id', $warehouse->id)->first()?->stock])
                        ->collapse()
                        ->all());

                    if (ModuleService::moduleIsEnabled('Order', $this->user)) {
                        $main['count_orders'] = $item->orders()->sum('count');
                    }

                    return $main;
                });

                $allData = $allData->merge($chunkData);
            });

        return $allData;
    }

    public function headings(): array
    {
        $main = array_merge(WbItemsImport::HEADERS, $this->warehouses->map(fn(WbWarehouse $warehouse) => 'Склад ' . $warehouse->name)->all());

        if (ModuleService::moduleIsEnabled('Order', $this->user)) {
            $main[] = 'Всего заказов';
        }

        return $main;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
