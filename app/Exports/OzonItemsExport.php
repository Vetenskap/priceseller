<?php

namespace App\Exports;

use App\Imports\OzonItemsImport;
use App\Models\Item;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\User;
use App\Services\ModuleService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Order\Models\Order;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OzonItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public Collection $warehouses;
    public User $user;

    public function __construct(public OzonMarket $market, public bool $template, public array $exportExtItemFields = [])
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
            ->with('itemable')
            ->chunk(1000, function (Collection $items) use (&$allData) {
                $chunkData = $items->map(function (OzonItem $item) {
                    $main = [
                        'product_id' => $item->product_id,
                        'offer_id' => $item->offer_id,
                        'item_code' => $item->itemable->code,
                        'item_type' => $item->itemable->getMorphClass() === 'App\Models\Item' ? 'Товар' : 'Комплект',
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
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at,
                        'delete' => 'Нет'
                    ];

                    $main = array_merge($main, $this->warehouses->map(fn(OzonWarehouse $warehouse) => ['Склад ' . $warehouse->name => $item->stocks()->where('ozon_warehouse_id', $warehouse->id)->first()?->stock])
                        ->collapse()
                        ->all());

                    if (ModuleService::moduleIsEnabled('Order', $this->user)) {
                        $main['count_orders'] = $item->orders()->sum('count');
                    }

                    foreach ($this->exportExtItemFields as $exportExtItemField) {
                        if (isset($item->itemable[$exportExtItemField])) {
                            $main['item.' . $exportExtItemField] = $item->itemable->{$exportExtItemField};
                        }
                    }

                    return $main;
                });

                $allData = $allData->merge($chunkData);
            });

        return $allData;
    }

    public function headings(): array
    {
        $main = array_merge(OzonItemsImport::HEADERS, $this->warehouses->map(fn(OzonWarehouse $warehouse) => 'Склад ' . $warehouse->name)->all());

        if (ModuleService::moduleIsEnabled('Order', $this->user)) {
            $main[] = 'Всего заказов';
        }

        foreach ($this->exportExtItemFields as $exportExtItemField) {
            if (isset(collect(Item::MAINATTRIBUTES)->where('name', $exportExtItemField)->first()['label'])) {
                $main[] = collect(Item::MAINATTRIBUTES)->where('name', $exportExtItemField)->first()['label'];
            }
        }

        return $main;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('C1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
