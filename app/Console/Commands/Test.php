<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use App\Events\TestBroadcast;
use App\Models\EmailSupplier;
use App\Models\Item;
use App\Models\OzonMarket;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Services\EmailPriceItemService;
use App\Services\EmailSupplierService;
use App\Services\OzonMarketService;
use App\Services\SupplierReportService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Maatwebsite\Excel\ChunkReader;
use Maatwebsite\Excel\Facades\Excel;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

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
        $user = User::first();
        $user->ozonMarkets()->orderBy('created_at')->get()->skip(5)->each(function (OzonMarket $market) {
            dump($market->name);
        });
    }
}
