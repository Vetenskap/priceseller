<?php

namespace Tests\Feature;

use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Services\OzonItemPriceService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OzonItemPriceServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_update_stock()
    {
        $supplier = Supplier::where('name', 'Восход')->first();
        $market = OzonMarket::findOrFail('9d865740-f7fc-4393-9dcb-3b1bf6da7a9b');
        $service = new OzonItemPriceService($supplier, $market, $supplier->warehouses()->pluck('id')->toArray());
        $service->updateStock();
    }
}
