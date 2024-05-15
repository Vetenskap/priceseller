<?php

namespace Tests\Feature;

use App\Models\WbMarket;
use App\Services\WbMarketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WbMarketServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_export(): void
    {
        $market = WbMarket::first();
        $service = new WbMarketService($market, 1);
        $service->exportItems();
    }

    public function test_import()
    {
        $market = WbMarket::first();
        $service = new WbMarketService($market, 1);
        $service->importItems('test/iviko.xlsx');
    }
}
