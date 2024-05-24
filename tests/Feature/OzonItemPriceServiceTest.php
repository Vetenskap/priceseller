<?php

namespace Tests\Feature;

use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Services\OzonItemPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OzonItemPriceServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $market = OzonMarket::find('7bd1f334-9270-459d-b335-8382d3f27ba9');
        $supplier = Supplier::find('9bd1f334-9270-429e-b335-8382d3f27ba9');

        $service = new OzonItemPriceService($supplier, $market);
        $service->updatePrice();
        $service->updateStock();
    }
}
