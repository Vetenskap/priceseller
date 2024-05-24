<?php

namespace Tests\Feature;

use App\Models\OzonMarket;
use App\Services\OzonMarketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OzonMarketServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_export(): void
    {
        $market = OzonMarket::find('7bd1f334-9270-459d-b335-8382d3f27ba9');
        $service = new OzonMarketService($market, 1);
        $service->exportItems();
    }

    public function test_import()
    {
        $market = OzonMarket::find('7bd1f334-9270-459d-b335-8382d3f27ba9');
        $service = new OzonMarketService($market, 1);
        $service->importItems('test/autoon.xlsx');
    }
}
