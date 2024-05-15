<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\WbMarket;
use App\Services\WbItemPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WbItemPriceServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $market = WbMarket::find('3be2f334-9270-459d-b335-7482d3f28ba3');
        $supplier = Supplier::find('9bd1f334-9270-429e-b335-8382d3f27ba9');

        $service = new WbItemPriceService($supplier, $market);
        $service->updatePrice();
        $service->updateStock();
    }
}
