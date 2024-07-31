<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;

class ItemWarehouseStocksExport implements FromCollection
{
    public function __construct(public Warehouse $warehouse)
    {
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
