<?php

namespace App\Exports;

use App\Imports\ItemsImport;
use App\Models\Item;
use App\Models\ItemAttribute;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\ModuleService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Moysklad\Models\Moysklad;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public Collection $warehouses;
    public Collection $attributes;
    public User $user;

    public function __construct(public int $userId, public bool $template = false)
    {
        $user = User::findOrFail($this->userId);
        $this->user = $user;
        $this->warehouses = $user->warehouses;
        $this->attributes = $user->itemAttributes;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->template) return collect();

        $allData = collect();

        Item::where('user_id', $this->userId)
            ->orderByDesc('updated_at')
            ->with(['attributesValues', 'warehousesStocks', 'supplier'])
            ->chunk(1000, function (Collection $items) use (&$allData) {
                $chunkData = $items->map(function (Item $item) {
                    $main = [
                        'ms_uuid' => $item->ms_uuid,
                        'code' => $item->code,
                        'name' => $item->name,
                        'supplier_name' => $item->supplier?->name,
                        'article' => $item->article,
                        'brand' => $item->brand,
                        'price' => $item->price,
                        'buy_price_reserve' => $item->buy_price_reserve,
                        'count' => $item->count,
                        'multiplicity' => $item->multiplicity,
                        'updated' => $item->updated ? 'Да' : 'Нет',
                        'unload_wb' => $item->unload_wb ? 'Да' : 'Нет',
                        'unload_ozon' => $item->unload_ozon ? 'Да' : 'Нет',
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at,
                        'delete' => 'Нет'
                    ];

                    $main = array_merge($main, $this->attributes->map(fn(ItemAttribute $attribute) => ['Доп. поле: ' . $attribute->name => $item->attributesValues->where('item_attribute_id', $attribute->id)->first()?->value])
                        ->collapse()
                        ->all());

                    $main = array_merge($main, $this->warehouses->map(fn(Warehouse $warehouse) => ['Склад ' . $warehouse->name => $item->warehousesStocks->where('warehouse_id', $warehouse->id)->first()?->stock])
                        ->collapse()
                        ->all());

                    if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
                        $main['count_orders'] = $item->moyskladOrders()->exists() ? $item->moyskladOrders()->sum('orders') : 0;
                    }

                    return $main;

                });

                $allData = $allData->merge($chunkData);
            });


        return $allData;
    }

    public function headings(): array
    {
        $main = ItemsImport::HEADERS;

        $main = array_merge($main, $this->attributes->map(fn(ItemAttribute $attribute) => 'Доп. поле:  ' . $attribute->name)->all());

        $main = array_merge($main, $this->warehouses->map(fn(Warehouse $warehouse) => 'Склад ' . $warehouse->name)->all());

        if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
            $main[] = 'Всего заказов';
        }

        return $main;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('M1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
