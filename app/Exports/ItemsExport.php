<?php

namespace App\Exports;

use App\Imports\ItemsImport;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ItemsExport implements FromCollection, WithHeadings, WithStyles
{
    public Collection $warehouses;

    public function __construct(public int $userId, public bool $template = false)
    {
        $this->warehouses = User::findOrFail($this->userId)->warehouses;
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
                        'count' => $item->count,
                        'multiplicity' => $item->multiplicity,
                        'updated' => $item->updated ? 'Да' : 'Нет',
                        'unload_wb' => $item->unload_wb ? 'Да' : 'Нет',
                        'unload_ozon' => $item->unload_ozon ? 'Да' : 'Нет',
                        'updated_at' => $item->updated_at,
                        'created_at' => $item->created_at,
                        'delete' => 'Нет'
                    ];

                    $main = array_merge($main, $this->warehouses->map(fn(Warehouse $warehouse) => ['Склад ' . $warehouse->name => $item->warehousesStocks()->where('warehouse_id', $warehouse->id)->first()?->stock])
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
        $main = ItemsImport::HEADERS;

        return array_merge($main, $this->warehouses->map(fn(Warehouse $warehouse) => 'Склад ' . $warehouse->name)->all());
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('E1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
        $sheet->getStyle('L1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
    }
}
