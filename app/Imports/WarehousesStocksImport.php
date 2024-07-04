<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WarehousesStocksImport implements ToCollection, WithHeadingRow
{
    public function __construct(public User $user)
    {
    }


    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {

        $collection->each(function (Collection $row) {

            $item = $this->user->items()->where('code', $row->get('Код'))->first();

            if (!$item) {
                return;
            }

            $row->each(function ($value, $key) use ($item) {
                if (str_starts_with($key, 'Склад')) {
                    if ($warehouse = $this->user->warehouses()->where('name', str_replace('Склад ', '', $key))->first()) {

                        $warehouse->stocks()->updateOrCreate(
                            [
                                'item_id' => $item->id
                            ],
                            [
                                'item_id' => $item->id,
                                'stock' => $value ?? 0
                            ]
                        );
                    }
                }
            });
        });
    }
}
