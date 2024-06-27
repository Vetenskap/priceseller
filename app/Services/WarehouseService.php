<?php

namespace App\Services;

use App\Exports\ItemWarehouseStocksExport;
use App\Imports\ItemWarehouseStocksImport;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class WarehouseService
{
    const PATH = 'users/warehouses/';

    public function __construct(public Warehouse $warehouse)
    {
    }


    public function importItems(string $uuid, string $ext): Collection
    {
        $import = new ItemWarehouseStocksImport($this->warehouse);
        \Excel::import($import, self::PATH . $uuid . '.' . $ext, 'public');
        return collect([
            'correct' => $import->correct,
            'error' => $import->error,
            'updated' => $import->updated,
            'deleted' => $import->deleted,
        ]);
    }

    public function exportItems(): string
    {
        $uuid = Str::uuid();
        \Excel::store(new ItemWarehouseStocksExport($this->warehouse), self::PATH . "$uuid.xlsx", 'public');
        return $uuid;
    }
}
