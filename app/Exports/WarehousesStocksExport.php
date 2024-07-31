<?php

namespace App\Exports;

use App\Imports\WarehousesStocksImport;
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

    public function __construct(public User $user, public bool $template = false)
    {
        $this->warehouses = $user->warehouses;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->template) {
            return collect();
        }

        $allData = collect();

        $this->user
            ->items()
            ->with(['warehousesStocks', 'supplier'])
            ->chunk(1000, function ($items) use (&$allData) {
                $chunkData = $items->map(function (Item $item) {
                    $main = [
                        'code' => $item->code,
                        'supplier_name' => $item->supplier->name,
                        'article' => $item->article,
                        'brand' => $item->brand,
                        'name' => $item->name,
                        'multiplicity' => $item->multiplicity,
                    ];

                    $warehouseStocks = $item->warehousesStocks->pluck('stock', 'warehouse.name');
                    $main = array_merge($main, $warehouseStocks->all());

                    return $main;
                });

                $allData = $allData->merge($chunkData);
            });

        return $allData;
    }

    public function headings(): array
    {
        return array_merge(WarehousesStocksImport::HEADERS, $this->warehouses->map(fn(Warehouse $warehouse) => 'Склад ' . $warehouse->name)->all());
    }
}
