<?php

namespace App\Console\Commands;

use App\Models\WbMarket;
use App\Services\WbMarketService;
use Illuminate\Console\Command;

class WbMarketServiceTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:market-service-test';

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
        $market = WbMarket::first();
        $service = new WbMarketService($market, 1);
        $service->directRelationships(collect([
            'min_price' => 250,
            'retail_markup_percent' => 35,
            'package' => 25,
        ]));
    }
}
