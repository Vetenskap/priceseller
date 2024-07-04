<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WarehousesStocksExport implements FromCollection, WithHeadings
{
    public Collection $warehouses;

    public function __construct(public User $user)
    {
        $this->warehouses = $user->warehouses;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $items = $this->user
            ->items()
            ->with(['warehousesStocks', 'supplier'])
            ->when(App::isLocal(), function (Builder$query) {
                $query->limit(1000);
            })
            ->get();

        return $items->map(function (Item $item) {
            $main = [
                'code' => $item->code,
                'supplier_name' => $item->supplier->name,
                'article' => $item->article,
                'brand' => $item->brand,
                'name' => $item->name,
                'multiplicity' => $item->multiplicity,

            ];

            $main = array_merge($main, $this->warehouses->map(fn(Warehouse $warehouse) => ['Склад ' . $warehouse->name => $item->warehousesStocks->where('warehouse_id', $warehouse->id)->first()?->stock])
                ->collapse()
                ->all());

            return $main;
        });
    }

    public function headings(): array
    {
        $main = [
            'Код',
            'Поставщик',
            'Артикул',
            'Бренд',
            'Наименование',
            'Кратность отгрузки',
        ];

        return array_merge($main, $this->warehouses->map(fn(Warehouse $warehouse) => 'Склад ' . $warehouse->name)->all());
    }
}
