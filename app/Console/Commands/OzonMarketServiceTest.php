<?php

namespace App\Console\Commands;

use App\Models\OzonMarket;
use App\Services\OzonMarketService;
use Illuminate\Console\Command;

class OzonMarketServiceTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ozon:market-service-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $market = OzonMarket::find('7bd1f334-9270-459d-b335-8382d3f27ba9');
        $service = new OzonMarketService($market, 1);
        $service->directRelationships(collect([
            'min_price_percent' => 22,
            'min_price' => 300,
            'shipping_processing' => 50,
        ]));
    }
}
