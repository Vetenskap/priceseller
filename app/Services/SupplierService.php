<?php

namespace App\Services;

use App\Models\Supplier;

class SupplierService
{
    CONST PATH = 'users/prices/';

    public static function setAllItemsUpdated(Supplier $supplier): void
    {
        $supplier->items()->where('buy_price_reserve', '>', 0)->update(['updated' => true]);
    }
}
